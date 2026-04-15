<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log; // Ensure this is imported

class MedicalStatusWebhookService
{
    public function notifyStudentStatus(User $user, string $event = 'completed'): bool
    {
        try {
            $url = trim((string) config('services.medical_status_webhook.url', ''));
            $secret = (string) config('services.medical_status_webhook.secret', '');

            if ($url === '' || $secret === '' || trim((string) $user->student_id) === '') {
                Log::warning('MedicalStatusWebhook: Missing configuration or student_id.', [
                    'student_id' => $user->student_id,
                    'url_configured' => !empty($url),
                ]);
                return false;
            }

            $payload = [
                'student_number' => (string) $user->student_id,
                'status' => $user->is_health_profile_completed ? 'cleared' : 'not_cleared',
                'timestamp' => now()->toIso8601String(),
            ];

            $rawPayload = json_encode($payload, JSON_UNESCAPED_SLASHES);
            if ($rawPayload === false) {
                return false;
            }

            $signature = hash_hmac('sha256', $rawPayload, $secret);
            $signatureHeader = trim((string) config('services.medical_status_webhook.signature_header', 'X-Medical-Signature'));

            Log::info('MedicalStatusWebhook: Sending payload.', ['student_id' => $user->student_id, 'payload' => $payload]);

            $response = Http::timeout((int) config('services.medical_status_webhook.timeout', 20))
                ->acceptJson()
                ->withHeaders([
                    $signatureHeader => $signature,
                ])
                ->withBody($rawPayload, 'application/json')
                ->post($url);

            if ($response->failed()) {
                Log::error('MedicalStatusWebhook: Failed to send.', [
                    'student_id' => $user->student_id,
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                return false;
            }

            Log::info('MedicalStatusWebhook: Success.', ['student_id' => $user->student_id, 'status' => $response->status()]);
            return true;

        } catch (\Throwable $exception) {
            Log::error('MedicalStatusWebhook: Exception caught.', [
                'message' => $exception->getMessage(),
                'trace' => $exception->getTraceAsString()
            ]);
            return false;
        }
    }
}