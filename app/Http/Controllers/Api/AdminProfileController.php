<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;

class AdminProfileController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        if (!Schema::hasTable('admins')) {
            return $this->errorResponse('Admins table was not found.', 404);
        }

        $query = Admin::query();
        $search = trim((string) $request->query('search', ''));

        if ($search !== '') {
            $query->where(function ($builder) use ($search) {
                foreach (['admin_id', 'name', 'first_name', 'last_name', 'email', 'email_address', 'office', 'access_level', 'role'] as $column) {
                    if (!Admin::hasColumn($column)) {
                        continue;
                    }

                    if ($column === 'admin_id') {
                        $builder->orWhere($column, 'like', '%' . $search . '%');
                    } else {
                        $builder->orWhere($column, 'like', '%' . $search . '%');
                    }
                }
            });
        }

        foreach (['admin_id', 'email', 'email_address', 'access_level', 'role'] as $column) {
            $value = trim((string) $request->query($column, ''));
            if ($value !== '' && Admin::hasColumn($column)) {
                $query->where($column, $value);
            }
        }

        $limit = max(1, min((int) $request->query('limit', 25), 100));
        $admins = $query->orderBy($this->defaultOrderColumn())
            ->limit($limit)
            ->get()
            ->map(fn (Admin $admin) => $this->transformAdmin($admin))
            ->values()
            ->all();

        return $this->successResponse($admins, 'Admin profiles retrieved successfully.');
    }

    public function lookup(Request $request): JsonResponse
    {
        if (!Schema::hasTable('admins')) {
            return $this->errorResponse('Admins table was not found.', 404);
        }

        $lookup = $this->resolveLookup($request);
        if ($lookup === null) {
            return $this->errorResponse('Admin profile not found.', 404);
        }

        [$column, $value] = $lookup;
        if (!Admin::hasColumn($column)) {
            return $this->errorResponse('Admin profile not found.', 404);
        }

        $admin = Admin::query()->where($column, $value)->first();
        if (!$admin) {
            return $this->errorResponse('Admin profile not found.', 404);
        }

        return $this->successResponse($this->transformAdmin($admin), 'Admin profile retrieved successfully.');
    }

    public function show($admin_id): JsonResponse
    {
        if (!Schema::hasTable('admins')) {
            return $this->errorResponse('Admins table was not found.', 404);
        }

        if (!Admin::hasColumn('admin_id')) {
            return $this->errorResponse('Admin profile not found.', 404);
        }

        $admin = Admin::query()->where('admin_id', $admin_id)->first();

        if (!$admin) {
            return $this->errorResponse('Admin profile not found.', 404);
        }

        return $this->successResponse($this->transformAdmin($admin), 'Admin profile retrieved successfully.');
    }

    public function externalShow($admin_id): JsonResponse
    {
        return $this->show($admin_id);
    }

    private function resolveLookup(Request $request): ?array
    {
        foreach (['admin_id', 'email', 'email_address', 'contact_no'] as $column) {
            $value = trim((string) $request->query($column, ''));
            if ($value !== '') {
                return [$column, $value];
            }
        }

        return null;
    }

    public function update(Request $request): JsonResponse
    {
        if (!Schema::hasTable('admins')) {
            return $this->errorResponse('Admins table was not found.', 404);
        }

        $validator = Validator::make($request->all(), [
            'admin_id' => 'required|exists:admins,admin_id',
            'email' => 'nullable|email',
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'birthday' => 'nullable|date',
            'age' => 'nullable|integer',
            'gender' => 'nullable|string|max:255',
            'civil_status' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'emergency_contact_person' => 'nullable|string|max:255',
            'emergency_contact_no' => 'nullable|string|max:255',
            'office' => 'nullable|string|max:255',
            'access_level' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'data' => [
                    'errors' => $validator->errors(),
                ],
                'message' => 'Validation failed.',
            ], 422);
        }

        $validated = $validator->validated();

        $admin = Admin::query()->where('admin_id', $validated['admin_id'])->first();
        if (!$admin) {
            return $this->errorResponse('Admin profile not found.', 404);
        }

        $payload = collect($validated)
            ->except('admin_id')
            ->all();

        $admin->fill($this->filterSupportedColumns($payload));
        $admin->save();

        return $this->successResponse($this->transformAdmin($admin->fresh()), 'Admin profile updated successfully.');
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
            'email' => ['email', 'email_address'],
            'office' => ['office', 'offices'],
            'address' => ['address'],
            'contact_no' => ['contact_no'],
            'emergency_contact_person' => ['emergency_contact_person'],
            'emergency_contact_no' => ['emergency_contact_no'],
            'age' => ['age'],
            'gender' => ['gender'],
            'birthday' => ['birthday'],
            'civil_status' => ['civil_status'],
            'access_level' => ['access_level', 'role', 'user_role', 'admin_role'],
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

    private function defaultOrderColumn(): string
    {
        foreach (['name', 'first_name', 'admin_id', 'id'] as $column) {
            if (Admin::hasColumn($column)) {
                return $column;
            }
        }

        return 'admin_id';
    }

    private function successResponse($data, string $message, int $status = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $data,
            'message' => $message,
        ], $status);
    }

    private function errorResponse(string $message, int $status): JsonResponse
    {
        return response()->json([
            'success' => false,
            'data' => null,
            'message' => $message,
        ], $status);
    }
}
