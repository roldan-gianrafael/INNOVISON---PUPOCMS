<?php

namespace App\Services;

use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class FacultySyncService
{
    public function generateHmacHeaders(string $method, string $url, string $body = '', string $nonce = ''): array
    {
        $secretKey = (string) config('services.pupt_flss.secret_key');
        $timestamp = (string) now()->timestamp;

        if ($secretKey === '') {
            throw new RuntimeException('PUPT-FLSS HMAC credentials are not configured.');
        }

        $normalizedMethod = strtoupper(trim($method));
        $message = implode('|', [
            $normalizedMethod,
            $url,
            $body,
            $timestamp,
            $nonce,
        ]);
        $signature = hash_hmac('sha256', $message, $secretKey);

        return [
            // FLSS expects a hex-encoded HMAC SHA-256 signature.
            'X-HMAC-Signature' => $signature,
            'X-HMAC-Timestamp' => $timestamp,
            'X-HMAC-Nonce' => $nonce,
        ];
    }

    public function fetchFaculties(): array
    {
        $baseUrl = trim((string) config('services.pupt_flss.faculty_profiles_url'));

        if ($baseUrl === '') {
            throw new RuntimeException('PUPT-FLSS faculty profiles URL is not configured.');
        }

        $timeout = (int) config('services.pupt_flss.timeout', 30);
        $response = Http::acceptJson()
            ->timeout($timeout)
            ->withHeaders($this->generateHmacHeaders('GET', $baseUrl))
            ->get($baseUrl);

        if (!$response->successful()) {
            throw new RequestException($response);
        }

        $payload = $response->json();

        return $this->extractFaculties($payload);
    }

    public function sync(): array
    {
        $faculties = $this->fetchFaculties();

        return [
            'fetched' => count($faculties),
            'synced' => 0,
        ];
    }

    private function extractFaculties($payload): array
    {
        if (!is_array($payload)) {
            return [];
        }

        if (isset($payload['faculties']) && is_array($payload['faculties'])) {
            return $payload['faculties'];
        }

        if (isset($payload['data']['faculties']) && is_array($payload['data']['faculties'])) {
            return $payload['data']['faculties'];
        }

        if (isset($payload['data']) && is_array($payload['data']) && $this->isList($payload['data'])) {
            return $payload['data'];
        }

        if ($this->isList($payload)) {
            return $payload;
        }

        return [];
    }
    private function isList(array $value): bool
    {
        if (function_exists('array_is_list')) {
            return array_is_list($value);
        }

        return array_keys($value) === range(0, count($value) - 1);
    }
}
