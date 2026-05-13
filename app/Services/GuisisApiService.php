<?php

namespace App\Services;

use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class GuisisApiService
{
    private string $baseUrl;
    private string $clientId;
    private string $clientSecret;
    private int $timeout;

    public function __construct()
    {
        $this->baseUrl = rtrim((string) config('services.guisis.base_url', ''), '/');
        $this->clientId = trim((string) config('services.guisis.client_id', ''));
        $this->clientSecret = trim((string) config('services.guisis.client_secret', ''));
        $this->timeout = (int) config('services.guisis.timeout', 20);
    }

    public function getStudentByEmailDetailed(string $email): array
    {
        return $this->get('/integrations/students/profile', ['email' => trim($email)]);
    }

    public function listStudentsDetailed(array $query = []): array
    {
        return $this->get('/integrations/students/profiles', array_filter($query, static fn ($value) => $value !== null && $value !== ''));
    }

    public function getStudentByStudentNumberDetailed(string $studentNumber): array
    {
        return $this->get('/integrations/students/' . rawurlencode(trim($studentNumber)));
    }

    public function getStudentAddressesDetailed(string $studentNumber): array
    {
        return $this->get('/integrations/students/' . rawurlencode(trim($studentNumber)) . '/addresses');
    }

    public function getStudentPersonalInfoDetailed(string $studentNumber): array
    {
        return $this->get('/integrations/students/' . rawurlencode(trim($studentNumber)) . '/personalInfo');
    }

    public function configuredBaseUrl(): string
    {
        return $this->baseUrl;
    }

    private function get(string $path, array $query = []): array
    {
        if ($this->baseUrl === '') {
            return [
                'status' => 500,
                'ok' => false,
                'message' => 'GuiSIS base URL is not configured.',
                'body' => '',
                'data' => null,
                'auth' => [
                    'status' => 'not_configured',
                    'source' => 'none',
                    'endpoint' => '',
                ],
            ];
        }

        try {
            $tokenMeta = $this->accessToken();
            $response = Http::timeout($this->timeout)
                ->acceptJson()
                ->withToken($tokenMeta['token'])
                ->get($this->baseUrl . $path, $query);

            $payload = $response->json();

            return [
                'status' => $response->status(),
                'ok' => $response->successful(),
                'message' => $response->successful()
                    ? 'GuiSIS request completed successfully.'
                    : $this->extractErrorMessage($payload, $response->body(), 'GuiSIS request returned an error response.'),
                'body' => $response->body(),
                'data' => $payload,
                'auth' => $tokenMeta['auth'],
            ];
        } catch (RequestException $exception) {
            $response = $exception->response;

            return [
                'status' => $response?->status() ?? 500,
                'ok' => false,
                'message' => $this->extractErrorMessage(
                    $response?->json(),
                    (string) ($response?->body() ?? ''),
                    'GuiSIS request failed.'
                ),
                'body' => (string) ($response?->body() ?? $exception->getMessage()),
                'data' => $response?->json(),
                'auth' => [
                    'status' => 'request_failed',
                    'source' => 'unknown',
                    'endpoint' => $this->baseUrl . '/auth/m2m/token',
                ],
            ];
        } catch (\Throwable $exception) {
            return [
                'status' => 500,
                'ok' => false,
                'message' => 'Unable to reach GuiSIS right now: ' . $exception->getMessage(),
                'body' => $exception->getMessage(),
                'data' => null,
                'auth' => [
                    'status' => str_contains(strtolower($exception->getMessage()), 'token') ? 'token_failed' : 'request_failed',
                    'source' => 'unknown',
                    'endpoint' => $this->baseUrl . '/auth/m2m/token',
                ],
            ];
        }
    }

    private function accessToken(): array
    {
        $cacheKey = 'guisis.m2m.' . md5($this->clientId . '|' . $this->baseUrl);
        $cachedToken = trim((string) Cache::get($cacheKey, ''));
        if ($cachedToken !== '') {
            return [
                'token' => $cachedToken,
                'auth' => [
                    'status' => 'ok',
                    'source' => 'cache',
                    'endpoint' => $this->baseUrl . '/auth/m2m/token',
                ],
            ];
        }

        if ($this->clientId === '' || $this->clientSecret === '') {
            throw new \RuntimeException('GuiSIS M2M credentials are incomplete.');
        }

        $response = Http::timeout($this->timeout)
            ->acceptJson()
            ->post($this->baseUrl . '/auth/m2m/token', [
                'clientId' => $this->clientId,
                'clientSecret' => $this->clientSecret,
            ]);

        if (!$response->successful()) {
            throw new \RuntimeException('Unable to fetch GuiSIS access token: ' . $response->body());
        }

        $payload = $response->json();
        $token = trim((string) ($payload['accessToken'] ?? $payload['access_token'] ?? ''));
        if ($token === '') {
            throw new \RuntimeException('GuiSIS access token response did not include accessToken.');
        }

        $expiresIn = (int) ($payload['expiresIn'] ?? $payload['expires_in'] ?? 3600);
        Cache::put($cacheKey, $token, now()->addSeconds(max(60, $expiresIn - 60)));

        return [
            'token' => $token,
            'auth' => [
                'status' => 'ok',
                'source' => 'fresh_m2m_token',
                'endpoint' => $this->baseUrl . '/auth/m2m/token',
            ],
        ];
    }

    private function extractErrorMessage($payload, string $body, string $fallback): string
    {
        if (is_array($payload)) {
            $message = trim((string) ($payload['message'] ?? $payload['error'] ?? ''));
            if ($message !== '') {
                return $message;
            }
        }

        $body = trim($body);
        return $body !== '' ? $body : $fallback;
    }
}
