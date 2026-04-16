<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class PuptasWebhookService
{
    private string $baseUrl;
    private string $clientId;
    private string $clientSecret;

    public function __construct()
    {
        $this->baseUrl      = rtrim(config('services.puptas.api_url'), '/');
        $this->clientId     = config('services.puptas.client_id');
        $this->clientSecret = config('services.puptas.client_secret');
    }

    /**
     * Get a temporary access token from the OAuth2 server
     */
    private function getAccessToken(): string
    {
        // Cache the token for 55 minutes to avoid constant re-authentication
        return Cache::remember('puptas_access_token', 3300, function () {
            $response = Http::asForm()->post($this->baseUrl . '/oauth/token', [
                'grant_type'    => 'client_credentials',
                'client_id'     => $this->clientId,
                'client_secret' => $this->clientSecret,
                'scope'         => '',
            ]);

            if ($response->failed()) {
                Log::error('PUPTAS Authentication failed', ['error' => $response->body()]);
                throw new \Exception('Failed to authenticate with PUPTAS: ' . $response->body());
            }

            return $response->json('access_token');
        });
    }

    /**
     * Send medical clearance notification to PUPTAS
     */
    public function sendMedicalClearance(string $student_id, ?string $student_number = null, int $isCleared = 1): array
    {
        try {
            $token = $this->getAccessToken();
            
            $payload = [
                'student_id' => $student_id,
                'is_health_profile_completed' => $isCleared
            ];

            if ($student_number) {
                $payload['student_number'] = $student_number;
            }

            $response = Http::withToken($token)
                ->timeout(30)
                ->post($this->baseUrl . '/api/v1/webhooks/medical-result', $payload);

            if ($response->successful()) {
                Log::info('PUPTAS webhook sent successfully', ['student_id' => $student_id]);
                return ['success' => true, 'message' => 'Synced successfully'];
            }

            Log::error('PUPTAS webhook failed', ['status' => $response->status(), 'error' => $response->body()]);
            return ['success' => false, 'message' => $response->body()];

        } catch (\Exception $e) {
            Log::error('PUPTAS webhook exception', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Send with automatic retry on failure
     */
    public function sendWithRetry(string $student_id, ?string $student_number = null, int $isCleared = 1, int $maxRetries = 3): array
    {
        $attempt = 0;
        while ($attempt < $maxRetries) {
            $result = $this->sendMedicalClearance($student_id, $student_number, $isCleared);
            if ($result['success']) return $result;

            $attempt++;
            if ($attempt < $maxRetries) sleep(5); // Wait 5 seconds before retrying
        }
        return ['success' => false, 'message' => 'Failed after ' . $maxRetries . ' attempts'];
    }
}