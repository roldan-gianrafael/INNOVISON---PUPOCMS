<?php

namespace Tests\Unit;

use App\Services\PuptasWebhookService;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PuptasWebhookServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        Carbon::setTestNow();
        Cache::forget('puptas.oauth_token');

        parent::tearDown();
    }

    public function test_it_sends_timestamped_headers_and_payload_to_puptas(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-04-28 23:35:02', 'Asia/Singapore'));

        Config::set('services.puptas.api_url', 'https://puptas.example/api/v1/medical/webhook');
        Config::set('services.puptas.client_id', 'client-id');
        Config::set('services.puptas.client_secret', 'client-secret');
        Config::set('services.puptas.webhook_secret', 'webhook-secret');
        Config::set('services.puptas.signature_header', 'X-Medical-Signature');
        Config::set('services.puptas.timestamp_header', 'X-HMAC-Timestamp');
        Config::set('services.puptas.hmac_signature_header', 'X-HMAC-Signature');
        Config::set('services.puptas.token_url', 'https://puptas.example/oauth/token');
        Config::set('services.puptas.timeout', 20);
        Cache::forget('puptas.oauth_token');

        Http::fake([
            'https://puptas.example/oauth/token' => Http::response([
                'access_token' => 'fake-token',
                'expires_in' => 3600,
            ], 200),
            'https://puptas.example/api/v1/medical/webhook' => Http::response([
                'message' => 'ok',
            ], 200),
        ]);

        $result = app(PuptasWebhookService::class)->sendMedicalClearance('2026-000-057', true);

        $this->assertTrue($result['success']);
        $timestamp = (string) Carbon::now()->timestamp;

        Http::assertSent(function (Request $request) use ($timestamp) {
            if ($request->url() !== 'https://puptas.example/api/v1/medical/webhook') {
                return false;
            }

            $payload = json_decode($request->body(), true);

            $this->assertIsArray($payload);
            $this->assertSame('2026-000-057', $payload['student_number'] ?? null);
            $this->assertSame('cleared', $payload['medical_status'] ?? null);
            $this->assertSame(1, $payload['is_health_profile_completed'] ?? null);
            $this->assertSame($timestamp, $payload['timestamp'] ?? null);
            $this->assertSame([$timestamp], $request->header('X-HMAC-Timestamp'));
            $this->assertSame([$timestamp], $request->header('X-Medical-Timestamp'));

            $legacySignature = hash_hmac('sha256', $request->body(), 'webhook-secret');
            $timestampedSignature = hash_hmac(
                'sha256',
                implode('|', [
                    'POST',
                    'https://puptas.example/api/v1/medical/webhook',
                    $request->body(),
                    $timestamp,
                    '',
                ]),
                'webhook-secret'
            );

            $this->assertSame([$legacySignature], $request->header('X-Medical-Signature'));
            $this->assertSame([$timestampedSignature], $request->header('X-HMAC-Signature'));

            return true;
        });
    }

    public function test_it_extracts_the_error_message_from_puptas_failures(): void
    {
        Config::set('services.puptas.api_url', 'https://puptas.example/api/v1/medical/webhook');
        Config::set('services.puptas.client_id', 'client-id');
        Config::set('services.puptas.client_secret', 'client-secret');
        Config::set('services.puptas.webhook_secret', 'webhook-secret');
        Config::set('services.puptas.signature_header', 'X-Medical-Signature');
        Config::set('services.puptas.timestamp_header', 'X-HMAC-Timestamp');
        Config::set('services.puptas.hmac_signature_header', 'X-HMAC-Signature');
        Config::set('services.puptas.token_url', 'https://puptas.example/oauth/token');
        Cache::forget('puptas.oauth_token');

        Http::fake([
            'https://puptas.example/oauth/token' => Http::response([
                'access_token' => 'fake-token',
                'expires_in' => 3600,
            ], 200),
            'https://puptas.example/api/v1/medical/webhook' => Http::response([
                'message' => 'Missing timestamp',
            ], 400),
        ]);

        $result = app(PuptasWebhookService::class)->sendMedicalClearance('2026-000-057', true);

        $this->assertFalse($result['success']);
        $this->assertSame('Missing timestamp', $result['message']);
    }
}
