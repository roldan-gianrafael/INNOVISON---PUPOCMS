<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MedicalStatusWebhookService
{
    public function notifyStudentStatus(User $user): bool
    {
        try {
            // 1. Get Config
            $baseUrl = 'https://puptas.undraftedbsit2027.com/api/v1';
            $clientId = config('services.puptas.client_id');
            $clientSecret = config('services.puptas.client_secret');
            $webhookSecret = config('services.puptas.webhook_secret');
            $signatureHeader = config('services.puptas.signature_header', 'X-Medical-Signature');

            // 2. Obtain OAuth Token
            $tokenResponse = Http::asForm()->post($baseUrl . '/oauth/token', [
                'grant_type' => 'client_credentials',
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
                'scope' => 'medical-write',
            ]);

            if ($tokenResponse->failed()) {
                Log::error('PUPTAS: OAuth Failed', ['body' => $tokenResponse->body()]);
                return false;
            }

            $token = $tokenResponse->json('access_token');

            // 3. Prepare Payload
            $payload = [
                'student_number' => (string) $user->student_id,
                'medical_status' => $user->is_health_profile_completed ? 'cleared' : 'failed',
            ];

            // Use JSON_UNESCAPED_SLASHES to ensure the string matches exactly what the receiver expects
            $rawPayload = json_encode($payload, JSON_UNESCAPED_SLASHES);
            
            // 4. Calculate HMAC Signature based on the RAW string
            $signature = hash_hmac('sha256', $rawPayload, $webhookSecret);

            // 5. Send Request using withBody() to guarantee byte-for-byte matching
            $response = Http::withToken($token)
                ->withHeaders([
                    $signatureHeader => $signature,
                    'Accept' => 'application/json',
                ])
                ->withBody($rawPayload, 'application/json')
                ->post($baseUrl . '/webhooks/medical-result');

            if ($response->failed()) {
                Log::error('PUPTAS: API Request Failed', [
                    'student_id' => $user->student_id,
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                return false;
            }

            return true;
        } catch (\Throwable $exception) {
            Log::error('PUPTAS: Exception', ['message' => $exception->getMessage()]);
            return false;
        }
    }
}