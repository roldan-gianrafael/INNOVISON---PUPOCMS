<?php

namespace App\Http\Controllers;

use App\Models\Admin;
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
        $lookupSearch = trim((string) $request->query('lookup_search', ''));
        $currentUserId = Auth::id();

        $allLocalUsers = $this->collectLocalUsers('');
        $allFacultyUsers = $this->collectFacultyUsers($facultySyncService, '');

        $localRecords = collect($allLocalUsers)
            ->map(function (array $record) use ($currentUserId) {
                $record['can_edit'] = $this->canManageRecord($record, $currentUserId);

                return $record;
            })
            ->filter(function (array $record) {
                $source = $record['source'] ?? 'student';
                $accessLevel = strtolower(trim((string) ($record['meta']['access_level'] ?? '')));

                if ($source === 'superadmin' || $source === 'student_assistant') {
                    return true;
                }

                return $source === 'admin' && $accessLevel !== 'designee';
            })
            ->sortBy(fn (array $record) => sprintf(
                '%02d-%s',
                $this->recordSortWeight($record['source'] ?? 'student'),
                strtolower((string) ($record['name'] ?? ''))
            ))
            ->values()
            ->all();

        $adminHubRecords = collect($allLocalUsers)
            ->map(function (array $record) use ($currentUserId) {
                $record['can_edit'] = $this->canManageRecord($record, $currentUserId);

                return $record;
            })
            ->filter(function (array $record) {
                return ($record['source'] ?? '') === 'admin'
                    && strtolower(trim((string) ($record['meta']['access_level'] ?? ''))) === 'designee';
            })
            ->sortBy(fn (array $record) => strtolower((string) ($record['name'] ?? '')))
            ->values()
            ->all();

        $lookupRecords = $lookupSearch !== ''
            ? collect($this->collectLocalUsers($lookupSearch))
                ->merge($this->collectFacultyUsers($facultySyncService, $lookupSearch))
                ->sortBy(fn (array $record) => sprintf(
                    '%02d-%s',
                    $this->recordSortWeight($record['source'] ?? 'student'),
                    strtolower((string) ($record['name'] ?? ''))
                ))
                ->values()
                ->all()
            : [];

        $stats = [
            'students' => collect($allLocalUsers)->where('source', 'student')->count(),
            'admins' => collect($allLocalUsers)->whereIn('source', ['admin', 'superadmin', 'student_assistant'])->count(),
            'faculty' => collect($allFacultyUsers)->count(),
            'active' => collect($allLocalUsers)->where('status', 'active')->count(),
            'inactive' => collect($allLocalUsers)->where('status', 'inactive')->count(),
            'local_total' => count($localRecords),
        ];

        return view('admin.user_management', [
            'lookupSearch' => $lookupSearch,
            'localRecords' => $localRecords,
            'adminHubRecords' => $adminHubRecords,
            'lookupRecords' => $lookupRecords,
            'stats' => $stats,
        ]);
    }

    public function update(Request $request, User $user)
    {
        $this->ensureCanManageUsers();

        $request->validate([
            'user_role' => ['required', Rule::in(['student', 'student_assistant', 'admin', 'superadmin', 'super_admin'])],
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'admin_email' => ['nullable', 'email', 'max:255'],
            'access_level' => ['nullable', Rule::in(['clinic_staff', 'designee'])],
            'office' => ['nullable', 'string', 'max:255'],
        ]);

        if ($this->isProtectedUser($user)) {
            return redirect()->back()->with('error', 'This account is protected and cannot be modified here.');
        }

        $originalRole = $user->user_role;
        $originalStatus = $user->status ?? 'active';
        $normalizedRequestedRole = User::normalizeRole($request->user_role);

        $user->user_role = $normalizedRequestedRole;
        $user->email = trim((string) $request->email);
        $adminLoginEmail = trim((string) $request->admin_email);

        if (Schema::hasColumn('users', 'status')) {
            $user->status = $request->status;
        }

        $user->save();

        $linkedAdmin = $this->findLinkedAdminProfile($user);
        if (in_array($normalizedRequestedRole, [User::ROLE_ADMIN, User::ROLE_SUPERADMIN], true)) {
            if (!$linkedAdmin) {
                $linkedAdmin = new Admin();
            }

            if (Admin::hasColumn('user_id')) {
                $linkedAdmin->user_id = $user->id;
            }

            if (Admin::hasColumn('first_name')) {
                $linkedAdmin->first_name = $user->first_name;
            }
            if (Admin::hasColumn('last_name')) {
                $linkedAdmin->last_name = $user->last_name;
            }
            if (Admin::hasColumn('email')) {
                $linkedAdmin->email = $adminLoginEmail !== '' ? $adminLoginEmail : $user->email;
            }
            if (Admin::hasColumn('email_address')) {
                $linkedAdmin->email_address = $adminLoginEmail !== '' ? $adminLoginEmail : $user->email;
            }
            if (Admin::hasColumn('name')) {
                $linkedAdmin->name = $user->name;
            }
            if (Admin::hasColumn('access_level')) {
                $linkedAdmin->access_level = match ($normalizedRequestedRole) {
                    User::ROLE_SUPERADMIN => 'superadmin',
                    User::ROLE_ADMIN => $request->filled('access_level') ? $request->access_level : 'clinic_staff',
                    default => null,
                };
            }
            if (Admin::hasColumn('status')) {
                $linkedAdmin->status = $request->status;
            }
            if (Admin::hasColumn('office')) {
                $linkedAdmin->office = $request->input('office');
            }

            $linkedAdmin->save();
        } elseif ($linkedAdmin) {
            if (Admin::hasColumn('access_level')) {
                $linkedAdmin->access_level = null;
            }
            if (Admin::hasColumn('email_address')) {
                $linkedAdmin->email_address = null;
            }
            if (Admin::hasColumn('status')) {
                $linkedAdmin->status = $request->status;
            }
            if (Admin::hasColumn('office') && $request->filled('office')) {
                $linkedAdmin->office = $request->input('office');
            }
            $linkedAdmin->save();
        }

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
            return redirect()->back()->with('error', 'This account access cannot be removed.');
        }

        $originalRole = $user->user_role;
        $originalStatus = $user->status ?? 'active';
        $adminProfileId = trim((string) request()->input('admin_profile_id', ''));

        $user->user_role = User::ROLE_STUDENT;
        if (Schema::hasColumn('users', 'status')) {
            $user->status = 'active';
        }
        $user->save();

        $linkedAdmin = null;
        if ($adminProfileId !== '' && Schema::hasTable('admins')) {
            $linkedAdmin = Admin::query()
                ->when(Admin::hasColumn('admin_id'), fn ($query) => $query->where('admin_id', $adminProfileId))
                ->first();
        }

        if (!$linkedAdmin) {
            $linkedAdmin = $this->findLinkedAdminProfile($user);
        }

        if ($linkedAdmin) {
            if (Admin::hasColumn('access_level')) {
                $linkedAdmin->access_level = null;
            }
            if (Admin::hasColumn('status')) {
                $linkedAdmin->status = 'active';
            }
            if (Admin::hasColumn('email_address')) {
                $linkedAdmin->email_address = null;
            }
            $linkedAdmin->save();
        }

        $this->logUserManagementAction(
            'Removed user access',
            sprintf(
                'Removed elevated access for %s (%s) and reset role from %s to student.',
                $user->name ?? $user->email,
                $user->email,
                $originalRole
            )
        );

        return redirect()->back()->with('success', 'User access removed successfully. The account is now back to the default student role.');
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
                $linkedAdmin = $this->findLinkedAdminProfile($user);
                $rawRole = strtolower(trim((string) ($user->user_role ?? 'student')));
                $role = User::normalizeRole($rawRole);
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
                    'role' => $this->resolveRoleLabel($rawRole, $linkedAdmin),
                    'raw_role' => $rawRole,
                    'normalized_role' => $role,
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
                        'access_level' => (string) ($linkedAdmin?->access_level ?? ''),
                        'admin_login_email' => (string) ($linkedAdmin?->email_address ?? $linkedAdmin?->email ?? ''),
                        'admin_profile_id' => $linkedAdmin?->admin_id,
                        'admin_profile_name' => (string) ($linkedAdmin?->name ?? ''),
                        'office' => (string) ($linkedAdmin?->office ?? ''),
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
        $rawRole = strtolower(trim((string) ($user->user_role ?? 'student')));
        $normalizedRole = User::normalizeRole($rawRole);

        if (in_array($rawRole, ['student_assistant', 'studentassistant', 'assistant'], true)) {
            return 'student_assistant';
        }

        return match ($normalizedRole) {
            User::ROLE_SUPERADMIN => 'superadmin',
            User::ROLE_ADMIN => 'admin',
            default => 'student',
        };
    }

    private function resolveUserSourceLabel(User $user): string
    {
        return match ($this->resolveUserSource($user)) {
            'superadmin' => 'Super Admin',
            'admin' => 'Admin',
            'student_assistant' => 'Student Assistant',
            default => 'Student',
        };
    }

    private function resolveRoleLabel(string $role, ?Admin $linkedAdmin = null): string
    {
        $rawRole = strtolower(trim($role));
        $normalizedRole = User::normalizeRole($rawRole);

        if (in_array($rawRole, ['student_assistant', 'studentassistant', 'assistant'], true)) {
            return 'Student Assistant';
        }

        if ($normalizedRole === User::ROLE_ADMIN && $linkedAdmin) {
            $accessLevel = strtolower(trim((string) ($linkedAdmin->access_level ?? '')));

            return match ($accessLevel) {
                'designee' => 'Admin - Designee',
                'clinic_staff', 'clinic staff', 'staff' => 'Admin - Clinic Staff',
                default => 'Admin',
            };
        }

        return match ($normalizedRole) {
            User::ROLE_SUPERADMIN => 'Super Admin',
            User::ROLE_ADMIN => 'Admin',
            default => 'Student',
        };
    }

    private function findLinkedAdminProfile(User $user): ?Admin
    {
        if (!Schema::hasTable('admins')) {
            return null;
        }

        if (Admin::hasColumn('user_id')) {
            $linkedByUserId = Admin::query()
                ->where('user_id', $user->id)
                ->first();

            if ($linkedByUserId) {
                return $linkedByUserId;
            }
        }

        $email = trim((string) ($user->email ?? ''));
        if ($email === '') {
            return null;
        }

        $linkedAdmin = Admin::query()
            ->where(function ($builder) use ($email) {
                if (Admin::hasColumn('email')) {
                    $builder->orWhere('email', $email);
                }

                if (Admin::hasColumn('email_address')) {
                    $builder->orWhere('email_address', $email);
                }
            })
            ->first();

        if ($linkedAdmin && Admin::hasColumn('user_id') && !$linkedAdmin->user_id) {
            $linkedAdmin->user_id = $user->id;
            $linkedAdmin->save();
        }

        return $linkedAdmin;
    }

    private function recordSortWeight(string $source): int
    {
        return match ($source) {
            'superadmin' => 0,
            'admin' => 1,
            'student_assistant' => 2,
            'student' => 3,
            'faculty' => 4,
            default => 9,
        };
    }

    private function isProtectedUser(User $user): bool
    {
        $currentUserId = Auth::id();
        $currentUser = Auth::user();
        $currentUserRole = User::normalizeRole((string) ($currentUser?->user_role ?? ''));
        $targetRole = User::normalizeRole((string) ($user->user_role ?? ''));

        if ($user->id === $currentUserId) {
            return true;
        }

        return $targetRole === User::ROLE_SUPERADMIN && $currentUserRole === User::ROLE_SUPERADMIN;
    }

    private function canManageRecord(array $record, ?int $currentUserId = null): bool
    {
        $currentUser = Auth::user();
        $currentUserRole = User::normalizeRole((string) ($currentUser?->user_role ?? ''));
        $recordRole = strtolower(trim((string) ($record['raw_role'] ?? $record['normalized_role'] ?? $record['source'] ?? 'student')));
        $recordId = (string) ($record['id'] ?? $record['record_id'] ?? '');

        if ($recordId !== '' && $currentUserId !== null && $recordId === (string) $currentUserId) {
            return false;
        }

        if (in_array($recordRole, ['superadmin', 'super_admin'], true) && $currentUserRole === User::ROLE_SUPERADMIN) {
            return false;
        }

        return true;
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
