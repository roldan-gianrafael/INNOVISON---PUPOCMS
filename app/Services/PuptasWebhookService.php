<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PuptasWebhookService
{
    private string $apiUrl;
    private string $clientId;
    private string $clientSecret;

    public function __construct()
    {
        // Use the full URL from config
        $this->apiUrl       = config('services.puptas.api_url');
        $this->clientId     = config('services.puptas.client_id');
        $this->clientSecret = config('services.puptas.client_secret');
    }

    public function sendMedicalClearance(string $student_id, ?string $student_number = null, int $isCleared = 1): array
    {
        try {
            $payload = [
                'student_id' => $student_id,
                'is_health_profile_completed' => $isCleared
            ];

            if ($student_number) {
                $payload['student_number'] = $student_number;
            }

            // Using Custom Headers as per many API standards
            $response = Http::timeout(30)
                ->withHeaders([
                    'X-Client-ID'     => $this->clientId,
                    'X-Client-Secret' => $this->clientSecret,
                    'Content-Type'    => 'application/json',
                    'Accept'          => 'application/json',
                ])
                ->post($this->apiUrl, $payload);

            if ($response->successful()) {
                Log::info('PUPTAS webhook sent successfully', ['student_id' => $student_id]);
                return ['success' => true, 'message' => 'Synced successfully'];
            }

            Log::error('PUPTAS webhook failed', [
                'status' => $response->status(), 
                'error'  => $response->body()
            ]);
            return ['success' => false, 'message' => $response->body()];

        } catch (\Exception $e) {
            Log::error('PUPTAS webhook exception', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function sendWithRetry(string $student_id, ?string $student_number = null, int $isCleared = 1, int $maxRetries = 3): array
    {
        $attempt = 0;
        while ($attempt < $maxRetries) {
            $result = $this->sendMedicalClearance($student_id, $student_number, $isCleared);
            if ($result['success']) return $result;
            $attempt++;
            if ($attempt < $maxRetries) sleep(2);
        }
        return ['success' => false, 'message' => 'Failed after ' . $maxRetries . ' attempts'];
    }
}