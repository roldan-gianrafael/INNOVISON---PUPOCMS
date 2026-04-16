<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PuptasWebhookService
{
    private string $apiUrl;
    private string $apiToken;

    public function __construct()
    {
        $this->apiUrl   = config('services.puptas.api_url');
        $this->apiToken = config('services.puptas.bearer_token');
    }

    /**
     * Fetch clearance status from PUPTAS API
     */
    public function getClearanceStatus(string $student_id): array
    {
        try {
            $baseUrl = str_replace('/webhooks/medical-result', '', $this->apiUrl);
            $response = Http::timeout(10)
                ->withToken($this->apiToken)
                ->get($baseUrl . '/api/v1/students/clearance/' . $student_id);

            return $response->successful() ? $response->json() : ['is_cleared' => false];
        } catch (\Exception $e) {
            Log::error('PUPTAS status fetch failed', ['error' => $e->getMessage()]);
            return ['is_cleared' => false];
        }
    }

    /**
     * Send webhook notification to PUPTAS
     */
    public function sendMedicalClearance(string $student_id, ?string $student_number = null, int $isCleared = 1): array
    {
        try {
            $payload = [
                'student_id' => $student_id,
                'is_health_profile_completed' => $isCleared,
                'student_number' => $student_number
            ];

            $response = Http::timeout(30)
                ->withToken($this->apiToken)
                ->withHeaders(['Content-Type' => 'application/json', 'Accept' => 'application/json'])
                ->post($this->apiUrl, $payload);

            if ($response->successful()) return ['success' => true];
            
            Log::error('PUPTAS webhook failed', ['error' => $response->body()]);
            return ['success' => false, 'message' => $response->body()];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}