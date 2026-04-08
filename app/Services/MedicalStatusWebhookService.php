<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Http;

class MedicalStatusWebhookService
{
    public function notifyStudentStatus(User $user, string $event = 'completed'): bool
    {
        try {
            $url = trim((string) config('services.medical_status_webhook.url', ''));
            $secret = (string) config('services.medical_status_webhook.secret', '');

            if ($url === '' || $secret === '' || trim((string) $user->student_id) === '') {
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

            Http::timeout((int) config('services.medical_status_webhook.timeout', 20))
                ->acceptJson()
                ->withHeaders([
                    $signatureHeader => $signature,
                ])
                ->withBody($rawPayload, 'application/json')
                ->post($url);

            return true;
        } catch (\Throwable $exception) {
            return false;
        }
    }
}
