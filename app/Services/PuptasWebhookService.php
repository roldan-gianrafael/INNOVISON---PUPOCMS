<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PuptasWebhookService
{
    private string $apiUrl;
    private string $clientId;
    private string $clientSecret;
    private string $webhookSecret;
    private string $signatureHeader;
    private string $timestampHeader;
    private string $hmacSignatureHeader;
    private int $timeout;
    private string $scope;
    private string $tokenUrl;

    public function __construct()
    {
        $this->apiUrl = (string) config('services.puptas.api_url', '');
        $this->clientId = (string) config('services.puptas.client_id', '');
        $this->clientSecret = (string) config('services.puptas.client_secret', '');
        $this->webhookSecret = (string) config('services.puptas.webhook_secret', '');
        $this->signatureHeader = (string) config('services.puptas.signature_header', 'X-Medical-Signature');
        $this->timestampHeader = (string) config('services.puptas.timestamp_header', 'X-HMAC-Timestamp');
        $this->hmacSignatureHeader = (string) config('services.puptas.hmac_signature_header', 'X-HMAC-Signature');
        $this->timeout = (int) config('services.puptas.timeout', 20);
        $this->scope = (string) config('services.puptas.scope', 'medical-read medical-write');
        $this->tokenUrl = $this->resolveTokenUrl((string) config('services.puptas.token_url', ''));
    }

    private function buildTimestampedSignature(string $method, string $url, string $payload, string $timestamp, string $nonce = ''): string
    {
        $message = implode('|', [
            strtoupper(trim($method)),
            trim($url),
            $payload,
            $timestamp,
            $nonce,
        ]);

        return hash_hmac('sha256', $message, $this->webhookSecret);
    }

    private function extractWebhookFailureMessage(string $responseBody): string
    {
        $responseBody = trim($responseBody);
        if ($responseBody === '') {
            return 'PUPTAS rejected the webhook request.';
        }

        $decoded = json_decode($responseBody, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            $message = trim((string) ($decoded['message'] ?? ''));
            if ($message !== '') {
                return $message;
            }
        }

        return $responseBody;
    }

    private function resolveTokenUrl(string $configuredTokenUrl): string
    {
        $configuredTokenUrl = trim($configuredTokenUrl);
        if ($configuredTokenUrl !== '') {
            return $configuredTokenUrl;
        }

        $apiUrl = trim($this->apiUrl);
        if ($apiUrl === '') {
            return '';
        }

        $parts = parse_url($apiUrl);
        if (!is_array($parts) || empty($parts['scheme']) || empty($parts['host'])) {
            return '';
        }

        $base = $parts['scheme'] . '://' . $parts['host'];
        if (!empty($parts['port'])) {
            $base .= ':' . $parts['port'];
        }

        return $base . '/oauth/token';
    }

    private function getAccessToken(): string
    {
        $cachedToken = trim((string) Cache::get('puptas.oauth_token', ''));
        if ($cachedToken !== '') {
            return $cachedToken;
        }

        if ($this->tokenUrl === '' || $this->clientId === '' || $this->clientSecret === '') {
            throw new \RuntimeException('PUPTAS OAuth configuration is incomplete.');
        }

        $response = Http::asForm()
            ->acceptJson()
            ->timeout($this->timeout)
            ->post($this->tokenUrl, [
                'grant_type' => 'client_credentials',
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'scope' => $this->scope,
            ]);

        if (!$response->successful()) {
            throw new \RuntimeException('Unable to fetch PUPTAS access token: ' . $response->body());
        }

        $token = trim((string) $response->json('access_token'));
        if ($token === '') {
            throw new \RuntimeException('PUPTAS access token response did not include access_token.');
        }

        $expiresIn = max(60, ((int) $response->json('expires_in', 3600)) - 60);
        Cache::put('puptas.oauth_token', $token, now()->addSeconds($expiresIn));

        return $token;
    }

    private function resolveApplicantsBaseUrl(): string
    {
        $apiUrl = trim($this->apiUrl);
        if ($apiUrl === '') {
            return '';
        }

        $parts = parse_url($apiUrl);
        if (!is_array($parts) || empty($parts['scheme']) || empty($parts['host'])) {
            return '';
        }

        $base = $parts['scheme'] . '://' . $parts['host'];
        if (!empty($parts['port'])) {
            $base .= ':' . $parts['port'];
        }

        return $base . '/api/v1/medical/applicants';
    }

    public function fetchApplicantByStudentNumber(string $studentNumber): ?array
    {
        $result = $this->fetchApplicantByStudentNumberDetailed($studentNumber);

        return $result['success'] ? ($result['data'] ?? null) : null;
    }

    public function fetchApplicantByStudentNumberDetailed(string $studentNumber): array
    {
        try {
            $studentNumber = trim($studentNumber);
            if ($studentNumber === '') {
                return [
                    'success' => false,
                    'status' => null,
                    'message' => 'Student number is required.',
                    'data' => null,
                ];
            }

            $applicantsBaseUrl = $this->resolveApplicantsBaseUrl();
            if ($applicantsBaseUrl === '') {
                return [
                    'success' => false,
                    'status' => null,
                    'message' => 'PUPTAS applicants endpoint is not configured.',
                    'data' => null,
                ];
            }

            $response = Http::timeout($this->timeout)
                ->withToken($this->getAccessToken())
                ->acceptJson()
                ->get(rtrim($applicantsBaseUrl, '/') . '/' . urlencode($studentNumber));

            if (!$response->successful()) {
                Log::warning('PUPTAS applicant lookup failed', [
                    'student_number' => $studentNumber,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return [
                    'success' => false,
                    'status' => $response->status(),
                    'message' => 'PUPTAS lookup failed with status ' . $response->status() . '.',
                    'body' => $response->body(),
                    'data' => null,
                ];
            }

            $data = $response->json('data');
            return [
                'success' => is_array($data),
                'status' => $response->status(),
                'message' => is_array($data) ? 'Applicant found.' : 'PUPTAS response did not include applicant data.',
                'body' => $response->body(),
                'data' => is_array($data) ? $data : null,
            ];
        } catch (\Throwable $exception) {
            Log::warning('PUPTAS applicant lookup exception', [
                'student_number' => $studentNumber,
                'error' => $exception->getMessage(),
            ]);
            return [
                'success' => false,
                'status' => null,
                'message' => $exception->getMessage(),
                'data' => null,
            ];
        }
    }

    public function fetchApplicantByIdpUserId(string $idpUserId): ?array
    {
        $result = $this->fetchApplicantByIdpUserIdDetailed($idpUserId);

        return $result['success'] ? ($result['data'] ?? null) : null;
    }

    public function fetchApplicantByIdpUserIdDetailed(string $idpUserId): array
    {
        try {
            $idpUserId = trim($idpUserId);
            if ($idpUserId === '') {
                return [
                    'success' => false,
                    'status' => null,
                    'message' => 'IDP user ID is required.',
                    'data' => null,
                ];
            }

            $applicantsBaseUrl = $this->resolveApplicantsBaseUrl();
            if ($applicantsBaseUrl === '') {
                return [
                    'success' => false,
                    'status' => null,
                    'message' => 'PUPTAS applicants endpoint is not configured.',
                    'data' => null,
                ];
            }

            $response = Http::timeout($this->timeout)
                ->withToken($this->getAccessToken())
                ->acceptJson()
                ->get(rtrim($applicantsBaseUrl, '/') . '/idp/' . urlencode($idpUserId));

            if (!$response->successful()) {
                Log::warning('PUPTAS applicant IDP lookup failed', [
                    'idp_user_id' => $idpUserId,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return [
                    'success' => false,
                    'status' => $response->status(),
                    'message' => 'PUPTAS IDP lookup failed with status ' . $response->status() . '.',
                    'body' => $response->body(),
                    'data' => null,
                ];
            }

            $data = $response->json('data');
            return [
                'success' => is_array($data),
                'status' => $response->status(),
                'message' => is_array($data) ? 'Applicant found.' : 'PUPTAS response did not include applicant data.',
                'body' => $response->body(),
                'data' => is_array($data) ? $data : null,
            ];
        } catch (\Throwable $exception) {
            Log::warning('PUPTAS applicant IDP lookup exception', [
                'idp_user_id' => $idpUserId,
                'error' => $exception->getMessage(),
            ]);
            return [
                'success' => false,
                'status' => null,
                'message' => $exception->getMessage(),
                'data' => null,
            ];
        }
    }

    public function sendMedicalClearance(string $studentNumber, bool $isCleared = true): array
    {
        try {
            $studentNumber = trim($studentNumber);
            if ($studentNumber === '') {
                return ['success' => false, 'message' => 'Student number is required.'];
            }

            if ($this->apiUrl === '' || $this->webhookSecret === '') {
                throw new \RuntimeException('PUPTAS webhook configuration is incomplete.');
            }

            $timestamp = (string) now()->timestamp;

            // PUPTAS production currently validates `is_health_profile_completed`
            // in addition to the documented `medical_status` field, so we send both.
            // We also include a timestamp so the receiving system can verify freshness.
            $payload = json_encode([
                'student_number' => $studentNumber,
                'medical_status' => $isCleared ? 'cleared' : 'failed',
                'is_health_profile_completed' => $isCleared ? 1 : 0,
                'timestamp' => $timestamp,
            ], JSON_UNESCAPED_SLASHES);

            if ($payload === false) {
                throw new \RuntimeException('Failed to encode PUPTAS payload.');
            }

            $legacySignature = hash_hmac('sha256', $payload, $this->webhookSecret);
            $timestampedSignature = $this->buildTimestampedSignature('POST', $this->apiUrl, $payload, $timestamp);
            $accessToken = $this->getAccessToken();

            $headers = [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                $this->timestampHeader => $timestamp,
                'X-Medical-Timestamp' => $timestamp,
                $this->hmacSignatureHeader => $timestampedSignature,
                $this->signatureHeader => $this->signatureHeader === $this->hmacSignatureHeader
                    ? $timestampedSignature
                    : $legacySignature,
            ];

            if ($this->timestampHeader !== 'X-HMAC-Timestamp') {
                $headers['X-HMAC-Timestamp'] = $timestamp;
            }

            if ($this->signatureHeader !== 'X-Medical-Signature') {
                $headers['X-Medical-Signature'] = $legacySignature;
            }

            $response = Http::timeout($this->timeout)
                ->withToken($accessToken)
                ->withHeaders($headers)
                ->withBody($payload, 'application/json')
                ->post($this->apiUrl);

            if ($response->successful()) {
                Log::info('PUPTAS webhook sent successfully', [
                    'student_number' => $studentNumber,
                    'timestamp' => $timestamp,
                ]);
                return ['success' => true, 'message' => 'Synced successfully'];
            }

            $errorMessage = $this->extractWebhookFailureMessage($response->body());
            Log::error('PUPTAS webhook failed', [
                'status' => $response->status(),
                'student_number' => $studentNumber,
                'timestamp' => $timestamp,
                'error' => $response->body(),
            ]);
            return ['success' => false, 'message' => $errorMessage];
        } catch (\Exception $e) {
            Log::error('PUPTAS webhook exception', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function sendWithRetry(string $studentNumber, bool $isCleared = true, int $maxRetries = 3): array
    {
        $attempt = 0;
        $lastResult = ['success' => false, 'message' => 'No webhook attempts were made.'];

        while ($attempt < $maxRetries) {
            $result = $this->sendMedicalClearance($studentNumber, $isCleared);
            $lastResult = $result;

            if ($result['success']) {
                return $result;
            }

            $attempt++;
            if ($attempt < $maxRetries) {
                sleep(2);
            }
        }

        return [
            'success' => false,
            'message' => trim((string) ($lastResult['message'] ?? '')) !== ''
                ? $lastResult['message']
                : ('Failed after ' . $maxRetries . ' attempts'),
        ];
    }
}
