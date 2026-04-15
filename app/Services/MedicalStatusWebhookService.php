<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PuptasWebhookService
{
private string $apiUrl = 'https://puptas.undraftedbsit2027.com/api/v1/webhooks/medical-result';
private string $apiToken;

public function __construct()
{
// Get token from config or .env
$this->apiToken = config('services.puptas.api_token');
}

/**
* Send medical clearance notification to PUPTAS
*
* @param string $studentId The student's UUID (your student_id field)
* @param string|null $studentNumber The student's number (optional)
* @param int $isCleared 1 = cleared/passed, 0 = failed
* @return array ['success' => bool, 'message' => string, 'response' => array|null]
*/
public function sendMedicalClearance(string $studentId, ?string $studentNumber = null, int $isCleared = 1): array
{
try {
// Prepare payload
$payload = [
'student_id' => $studentId,
'is_health_profile_completed' => $isCleared
];

// Add student number if available
if ($studentNumber) {
$payload['student_number'] = $studentNumber;
}

// Send request
$response = Http::timeout(30)
->withHeaders([
'Authorization' => 'Bearer ' . $this->apiToken,
'Content-Type' => 'application/json',
'Accept' => 'application/json',
])
->post($this->apiUrl, $payload);

// Check response
if ($response->successful()) {
Log::info('PUPTAS webhook sent successfully', [
'student_id' => $studentId,
'student_number' => $studentNumber,
'status' => $isCleared,
'response' => $response->json()
]);

return [
'success' => true,
'message' => 'Medical clearance sent to PUPTAS successfully',
'response' => $response->json()
];
} else {
Log::error('PUPTAS webhook failed', [
'student_id' => $studentId,
'status_code' => $response->status(),
'error' => $response->body()
]);

return [
'success' => false,
'message' => 'Failed to send to PUPTAS: ' . $response->body(),
'response' => $response->json()
];
}

} catch (\Exception $e) {
Log::error('PUPTAS webhook exception', [
'student_id' => $studentId,
'error' => $e->getMessage(),
'trace' => $e->getTraceAsString()
]);

return [
'success' => false,
'message' => 'Exception: ' . $e->getMessage(),
'response' => null
];
}
}

/**
* Send with automatic retry on failure
*
* @param string $studentId
* @param string|null $studentNumber
* @param int $isCleared
* @param int $maxRetries
* @return array
*/
public function sendWithRetry(string $studentId, ?string $studentNumber = null, int $isCleared = 1, int $maxRetries = 3): array
{
$attempt = 0;
$delays = [5, 30, 300]; // seconds: 5s, 30s, 5min

while ($attempt < $maxRetries) {
$result = $this->sendMedicalClearance($studentId, $studentNumber, $isCleared);

if ($result['success']) {
return $result;
}

$attempt++;
if ($attempt < $maxRetries) {
Log::warning("PUPTAS webhook retry attempt {$attempt} for student {$studentId}");
sleep($delays[$attempt - 1]);
}
}

// All retries failed
Log::critical('PUPTAS webhook failed after all retries', [
'student_id' => $studentId,
'attempts' => $maxRetries
]);

return [
'success' => false,
'message' => 'Failed after ' . $maxRetries . ' attempts',
'response' => null
];
}
}