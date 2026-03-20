<?php

namespace App\Services;

use App\Models\Admin;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

class FacultySyncService
{
    public function generateHmacHeaders(): array
    {
        $systemId = (string) config('services.pupt_flss.system_id');
        $secretKey = (string) config('services.pupt_flss.secret_key');
        $timestamp = (string) now()->timestamp;
        $nonce = Str::random(16);

        if ($systemId === '' || $secretKey === '') {
            throw new RuntimeException('PUPT-FLSS HMAC credentials are not configured.');
        }

        $signature = hash_hmac('sha256', $systemId . $timestamp . $nonce, $secretKey);

        return [
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

        $response = Http::acceptJson()
            ->timeout((int) config('services.pupt_flss.timeout', 30))
            ->withHeaders($this->generateHmacHeaders())
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
        $synced = 0;

        foreach ($faculties as $faculty) {
            if (!is_array($faculty)) {
                continue;
            }

            $adminId = trim((string) ($faculty['faculty_code'] ?? ''));
            if ($adminId === '') {
                continue;
            }

            $attributes = $this->buildAdminAttributes($faculty);

            Admin::query()->updateOrCreate(
                ['admin_id' => $adminId],
                $attributes
            );

            $synced++;
        }

        return [
            'fetched' => count($faculties),
            'synced' => $synced,
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

    private function buildAdminAttributes(array $faculty): array
    {
        $attributes = [
            'admin_id' => (string) ($faculty['faculty_code'] ?? ''),
            'email_address' => $faculty['email'] ?? null,
            'name' => trim(implode(' ', array_filter([
                $faculty['first_name'] ?? null,
                $faculty['last_name'] ?? null,
            ]))),
            'role' => $faculty['faculty_type'] ?? null,
        ];

        $optionalMap = [
            'first_name' => 'first_name',
            'last_name' => 'last_name',
            'offices' => 'offices',
            'address' => 'address',
            'contact_no' => 'contact_no',
            'emergency_contact_person' => 'emergency_contact_person',
            'emergency_contact_no' => 'emergency_contact_no',
            'age' => 'age',
            'gender' => 'gender',
            'birthday' => 'birthday',
            'civil_status' => 'civil_status',
            'status' => 'status',
            'is_active' => 'is_active',
        ];

        foreach ($optionalMap as $localColumn => $remoteField) {
            if (array_key_exists($remoteField, $faculty)) {
                $attributes[$localColumn] = $faculty[$remoteField];
            }
        }

        return $this->filterSupportedColumns($attributes);
    }

    private function filterSupportedColumns(array $attributes): array
    {
        $filtered = [];

        foreach ($attributes as $column => $value) {
            if (Admin::hasColumn($column)) {
                $filtered[$column] = $value;
            }
        }

        return $filtered;
    }

    private function isList(array $value): bool
    {
        if (function_exists('array_is_list')) {
            return array_is_list($value);
        }

        return array_keys($value) === range(0, count($value) - 1);
    }
}
