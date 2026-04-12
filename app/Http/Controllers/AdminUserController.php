<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\HealthProfile;
use App\Models\User;
use App\Services\FacultySyncService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class AdminUserController extends Controller
{
    public function index(Request $request, FacultySyncService $facultySyncService)
    {
        $search = trim((string) $request->query('search', ''));
        $localUsers = $this->collectLocalUsers($search);
        $facultyUsers = $this->collectFacultyUsers($facultySyncService, $search);

        $records = collect($localUsers)
            ->merge($facultyUsers)
            ->sortBy(fn (array $record) => sprintf(
                '%02d-%s',
                $this->recordSortWeight($record['source'] ?? 'student'),
                strtolower((string) ($record['name'] ?? ''))
            ))
            ->values()
            ->all();

        $stats = [
            'students' => collect($localUsers)->where('source', 'student')->count(),
            'admins' => collect($localUsers)->whereIn('source', ['admin', 'superadmin'])->count(),
            'faculty' => collect($facultyUsers)->count(),
            'active' => collect($records)->where('status', 'active')->count(),
            'inactive' => collect($records)->where('status', 'inactive')->count(),
        ];

        return view('admin.user_management', compact('search', 'records', 'stats'));
    }

    public function update(Request $request, User $user)
    {
        $this->ensureCanManageUsers();

        $request->validate([
            'user_role' => ['required', Rule::in(['student', 'student_assistant', 'admin', 'superadmin', 'super_admin'])],
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
        ]);

        if ($this->isProtectedUser($user)) {
            return redirect()->back()->with('error', 'This account is protected and cannot be modified here.');
        }

        $originalRole = $user->user_role;
        $originalStatus = $user->status ?? 'active';

        $user->user_role = User::normalizeRole($request->user_role);
        $user->email = trim((string) $request->email);

        if (Schema::hasColumn('users', 'status')) {
            $user->status = $request->status;
        }

        $user->save();

        $this->logUserManagementAction(
            'Updated user account',
            sprintf(
                'Updated %s (%s) role from %s to %s and status from %s to %s.',
                $user->name ?? $user->email,
                $user->email,
                $originalRole,
                $user->user_role,
                $originalStatus,
                $user->status ?? 'active'
            )
        );

        return redirect()->back()->with('success', 'User account updated successfully.');
    }

    public function destroy(User $user)
    {
        $this->ensureCanManageUsers();

        if ($this->isProtectedUser($user) || $user->id === Auth::id()) {
            return redirect()->back()->with('error', 'This account cannot be deleted.');
        }

        DB::transaction(function () use ($user) {
            if (Schema::hasTable('health_profiles')) {
                $user->healthProfile()->delete();
            }

            if (Schema::hasTable('appointments')) {
                Appointment::where('user_id', $user->id)->delete();
            }

            $user->delete();
        });

        $this->logUserManagementAction(
            'Deleted user account',
            sprintf('Deleted user account for %s (%s).', $user->name ?? $user->email, $user->email)
        );

        return redirect()->back()->with('success', 'User account deleted successfully.');
    }

    private function collectLocalUsers(string $search): array
    {
        $query = User::query()->with('healthProfile');

        if ($search !== '') {
            $query->where(function ($builder) use ($search) {
                foreach ([
                    'student_id',
                    'first_name',
                    'last_name',
                    'name',
                    'email',
                    'course',
                    'year',
                    'section',
                    'user_role',
                ] as $column) {
                    if (Schema::hasColumn('users', $column)) {
                        $builder->orWhere($column, 'like', '%' . $search . '%');
                    }
                }
            });
        }

        return $query->orderBy('first_name')
            ->limit(100)
            ->get()
            ->map(function (User $user) {
                $role = User::normalizeRole((string) ($user->user_role ?? 'student'));
                $status = strtolower(trim((string) ($user->status ?? 'active')));
                if ($status === '') {
                    $status = 'active';
                }

                $displayName = trim((string) $user->name);
                if ($displayName === '') {
                    $displayName = trim(implode(' ', array_filter([
                        $user->first_name ?? '',
                        $user->last_name ?? '',
                    ])));
                }

                $studentPhoto = $user->healthProfile?->student_photo;

                return [
                    'id' => (string) $user->id,
                    'record_id' => (string) $user->id,
                    'source' => $this->resolveUserSource($user),
                    'source_label' => $this->resolveUserSourceLabel($user),
                    'name' => $displayName !== '' ? $displayName : ($user->email ?? 'Unknown User'),
                    'first_name' => (string) ($user->first_name ?? ''),
                    'last_name' => (string) ($user->last_name ?? ''),
                    'student_id' => (string) ($user->student_id ?? ''),
                    'email' => (string) ($user->email ?? ''),
                    'role' => $this->prettyRoleLabel($role),
                    'raw_role' => $role,
                    'status' => $status === 'inactive' ? 'inactive' : 'active',
                    'avatar_url' => $studentPhoto ? asset('storage/' . $studentPhoto) : null,
                    'avatar_letter' => strtoupper(substr($displayName !== '' ? $displayName : ($user->email ?? 'U'), 0, 1)),
                    'can_edit' => true,
                    'is_external' => false,
                    'meta' => [
                        'email' => (string) ($user->email ?? ''),
                        'course' => (string) ($user->course ?? ''),
                        'year' => (string) ($user->year ?? ''),
                        'section' => (string) ($user->section ?? ''),
                        'DOB' => (string) ($user->DOB ?? ''),
                        'gender' => (string) ($user->gender ?? ''),
                        'contact_no' => (string) ($user->contact_no ?? ''),
                        'is_health_profile_completed' => (bool) ($user->is_health_profile_completed ?? false),
                        'updated_at' => optional($user->updated_at)->toIso8601String(),
                    ],
                ];
            })
            ->values()
            ->all();
    }

    private function collectFacultyUsers(FacultySyncService $facultySyncService, string $search): array
    {
        try {
            $faculties = $facultySyncService->fetchFaculties($search);
        } catch (\Throwable $exception) {
            return [];
        }

        return collect($faculties)
            ->filter(fn ($faculty) => is_array($faculty))
            ->map(function (array $faculty) {
                $profile = is_array($faculty['profile'] ?? null) ? $faculty['profile'] : [];
                $name = trim((string) ($faculty['name'] ?? trim(implode(' ', array_filter([
                    $faculty['first_name'] ?? '',
                    $faculty['middle_name'] ?? '',
                    $faculty['last_name'] ?? '',
                    $faculty['suffix_name'] ?? '',
                ])))));
                $email = trim((string) ($faculty['email'] ?? ''));
                $role = trim((string) ($faculty['faculty_type'] ?? $faculty['role'] ?? $faculty['access_level'] ?? 'Faculty'));
                $status = strtolower(trim((string) ($faculty['status'] ?? 'active')));
                $recordId = (string) ($faculty['faculty_id'] ?? $faculty['faculty_code'] ?? $faculty['id'] ?? ($email !== '' ? $email : 'faculty'));

                if (in_array($status, ['1', 'true', 'active', 'enabled'], true)) {
                    $status = 'active';
                } elseif (in_array($status, ['0', 'false', 'inactive', 'disabled'], true)) {
                    $status = 'inactive';
                } else {
                    $status = $status !== '' ? $status : 'active';
                }

                return [
                    'id' => $recordId,
                    'record_id' => $recordId,
                    'source' => 'faculty',
                    'source_label' => 'Faculty',
                    'name' => $name !== '' ? $name : ($email !== '' ? $email : 'Faculty'),
                    'first_name' => (string) ($faculty['first_name'] ?? ''),
                    'last_name' => (string) ($faculty['last_name'] ?? ''),
                    'student_id' => (string) ($faculty['faculty_code'] ?? ''),
                    'email' => $email,
                    'role' => $role !== '' ? $role : 'Faculty',
                    'raw_role' => $role,
                    'status' => $status,
                    'avatar_url' => null,
                    'avatar_letter' => strtoupper(substr($name !== '' ? $name : ($email ?: 'F'), 0, 1)),
                    'can_edit' => false,
                    'is_external' => true,
                    'meta' => [
                        'faculty_id' => $faculty['faculty_id'] ?? null,
                        'faculty_code' => $faculty['faculty_code'] ?? null,
                        'faculty_type' => $faculty['faculty_type'] ?? null,
                        'department' => $faculty['department'] ?? null,
                        'profile' => $profile,
                        'updated_at' => $faculty['last_updated'] ?? null,
                    ],
                ];
            })
            ->values()
            ->all();
    }

    private function resolveUserSource(User $user): string
    {
        return match (User::normalizeRole((string) ($user->user_role ?? 'student'))) {
            User::ROLE_SUPERADMIN => 'superadmin',
            User::ROLE_ADMIN => 'admin',
            default => 'student',
        };
    }

    private function resolveUserSourceLabel(User $user): string
    {
        return match ($this->resolveUserSource($user)) {
            'superadmin' => 'Super Admin',
            'admin' => 'Student Assistant',
            default => 'Student',
        };
    }

    private function prettyRoleLabel(string $role): string
    {
        return match (User::normalizeRole($role)) {
            User::ROLE_SUPERADMIN => 'Super Admin',
            User::ROLE_ADMIN => 'Student Assistant',
            default => 'Student',
        };
    }

    private function recordSortWeight(string $source): int
    {
        return match ($source) {
            'superadmin' => 0,
            'admin' => 1,
            'student' => 2,
            'faculty' => 3,
            default => 9,
        };
    }

    private function isProtectedUser(User $user): bool
    {
        return $user->id === Auth::id() || User::normalizeRole((string) ($user->user_role ?? '')) === User::ROLE_SUPERADMIN;
    }

    private function ensureCanManageUsers(): void
    {
        $current = Auth::user();
        abort_unless($current && User::normalizeRole((string) ($current->user_role ?? '')) === User::ROLE_SUPERADMIN, 403);
    }

    private function logUserManagementAction(string $action, string $description): void
    {
        $user = Auth::user();
        if (!$user) {
            return;
        }

        \App\Models\ActivityLog::create([
            'user_id' => $user->id,
            'user_name' => $user->name ?? $user->email ?? 'Unknown User',
            'user_role' => strtolower((string) ($user->user_role ?? '')),
            'action' => $action,
            'module' => 'user_management',
            'event_type' => 'administrative_action',
            'description' => $description,
            'route_name' => optional(request()->route())->getName(),
            'http_method' => strtoupper((string) request()->method()),
            'request_path' => '/' . ltrim((string) request()->path(), '/'),
            'status_code' => 200,
            'ip_address' => request()->ip(),
            'user_agent' => substr((string) request()->userAgent(), 0, 255),
        ]);
    }
}
