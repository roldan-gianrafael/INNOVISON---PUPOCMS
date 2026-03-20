<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class AdminProfileController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        if (!Schema::hasTable('admins')) {
            return response()->json([
                'message' => 'Admins table was not found.',
            ], 404);
        }

        $lookup = $this->resolveLookup($request);

        if ($lookup === null) {
            return response()->json([
                'message' => 'Admin profile not found.',
            ], 404);
        }

        [$column, $value] = $lookup;

        if (!Admin::hasColumn($column)) {
            return response()->json([
                'message' => 'Admin profile not found.',
            ], 404);
        }

        $admin = Admin::query()->where($column, $value)->first();

        if (!$admin) {
            return response()->json([
                'message' => 'Admin profile not found.',
            ], 404);
        }

        return response()->json([
            'data' => $this->transformAdmin($admin),
        ]);
    }

    private function resolveLookup(Request $request): ?array
    {
        foreach (['admin_id', 'email_address', 'contact_no'] as $column) {
            $value = trim((string) $request->query($column, ''));
            if ($value !== '') {
                return [$column, $value];
            }
        }

        return null;
    }

    private function transformAdmin(Admin $admin): array
    {
        $data = [];

        foreach ($this->responseFieldMap() as $outputField => $candidateColumns) {
            $value = $this->pickFirstAvailableValue($admin, $candidateColumns);
            if ($value !== null && $value !== '') {
                $data[$outputField] = $value;
            }
        }

        if (!isset($data['name'])) {
            $name = trim(implode(' ', array_filter([
                $this->pickFirstAvailableValue($admin, ['first_name']),
                $this->pickFirstAvailableValue($admin, ['last_name']),
            ])));

            if ($name !== '') {
                $data['name'] = $name;
            }
        }

        return $data;
    }

    private function responseFieldMap(): array
    {
        return [
            'admin_id' => ['admin_id', 'id'],
            'first_name' => ['first_name'],
            'last_name' => ['last_name'],
            'name' => ['name', 'full_name'],
            'email_address' => ['email_address', 'email'],
            'offices' => ['offices'],
            'address' => ['address'],
            'contact_no' => ['contact_no'],
            'emergency_contact_person' => ['emergency_contact_person'],
            'emergency_contact_no' => ['emergency_contact_no'],
            'age' => ['age'],
            'gender' => ['gender'],
            'birthday' => ['birthday'],
            'civil_status' => ['civil_status'],
            'role' => ['role', 'user_role', 'admin_role'],
            'status' => ['status'],
            'is_active' => ['is_active'],
        ];
    }

    private function pickFirstAvailableValue(Admin $admin, array $candidateColumns)
    {
        foreach ($candidateColumns as $column) {
            if (Admin::hasColumn($column)) {
                return $admin->getAttribute($column);
            }
        }

        return null;
    }
}
