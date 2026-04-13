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

        $adminHubRecords = $this->collectAdminHubProfiles($lookupSearch);

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
        $requestedRoleRaw = strtolower(trim((string) $request->user_role));
        $usesSeparateAdminEmail = in_array($requestedRoleRaw, ['student_assistant', 'studentassistant', 'assistant'], true);
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
                $linkedAdmin->email = $usesSeparateAdminEmail && $adminLoginEmail !== '' ? $adminLoginEmail : $user->email;
            }
            if (Admin::hasColumn('email_address')) {
                $linkedAdmin->email_address = $usesSeparateAdminEmail && $adminLoginEmail !== '' ? $adminLoginEmail : $user->email;
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

    public function storeFromLookup(Request $request)
    {
        $this->ensureCanManageUsers();

        $request->validate([
            'lookup_source' => ['required', Rule::in(['faculty'])],
            'management_view' => ['nullable', Rule::in(['account-access', 'admin-hub'])],
            'user_role' => ['required', Rule::in(['student', 'student_assistant', 'admin', 'superadmin', 'super_admin'])],
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'email' => ['required', 'email', 'max:255'],
            'admin_email' => ['nullable', 'email', 'max:255'],
            'access_level' => ['nullable', Rule::in(['clinic_staff', 'designee'])],
            'office' => ['nullable', 'string', 'max:255'],
            'first_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
            'full_name' => ['nullable', 'string', 'max:255'],
            'external_identifier' => ['nullable', 'string', 'max:255'],
        ]);

        $normalizedRequestedRole = User::normalizeRole($request->user_role);
        $requestedRoleRaw = strtolower(trim((string) $request->user_role));
        $usesSeparateAdminEmail = in_array($requestedRoleRaw, ['student_assistant', 'studentassistant', 'assistant'], true);
        $baseEmail = trim((string) $request->email);

        $managementView = trim((string) $request->input('management_view', 'account-access'));
        $firstName = trim((string) $request->input('first_name', ''));
        $lastName = trim((string) $request->input('last_name', ''));
        $fullName = trim((string) $request->input('full_name', ''));
        if ($fullName === '') {
            $fullName = trim(implode(' ', array_filter([$firstName, $lastName])));
        }

        if ($managementView === 'admin-hub') {
            $adminEmail = $baseEmail;
            $externalIdentifier = trim((string) $request->input('external_identifier', ''));
            $linkedAdmin = Admin::query()
                ->where(function ($query) use ($adminEmail, $baseEmail) {
                    if (Admin::hasColumn('email')) {
                        $query->orWhere('email', $adminEmail)->orWhere('email', $baseEmail);
                    }
                    if (Admin::hasColumn('email_address')) {
                        $query->orWhere('email_address', $adminEmail)->orWhere('email_address', $baseEmail);
                    }
                })
                ->first() ?? new Admin();

            if (Admin::hasColumn('first_name')) {
                $linkedAdmin->first_name = $firstName !== '' ? $firstName : 'Faculty';
            }
            if (Admin::hasColumn('last_name')) {
                $linkedAdmin->last_name = $lastName !== '' ? $lastName : 'User';
            }
            if (Admin::hasColumn('name')) {
                $linkedAdmin->name = $fullName !== '' ? $fullName : trim(($linkedAdmin->first_name ?? 'Faculty') . ' ' . ($linkedAdmin->last_name ?? 'User'));
            }
            if (Admin::hasColumn('email')) {
                $linkedAdmin->email = $adminEmail;
            }
            if (Admin::hasColumn('email_address')) {
                $linkedAdmin->email_address = $adminEmail;
            }
            if (Admin::hasColumn('external_identifier')) {
                $linkedAdmin->external_identifier = $externalIdentifier !== '' ? $externalIdentifier : null;
            }
            if (Admin::hasColumn('access_level')) {
                $linkedAdmin->access_level = 'designee';
            }
            if (Admin::hasColumn('status')) {
                $linkedAdmin->status = $request->status;
            }
            if (Admin::hasColumn('office')) {
                $linkedAdmin->office = $request->input('office');
            }
            $linkedAdmin->save();

            $this->logUserManagementAction(
                'Added admin hub profile from lookup',
                sprintf(
                    'Added %s (%s) from %s lookup into the clinic admin hub as designee only.',
                    $linkedAdmin->name ?? $adminEmail,
                    $adminEmail,
                    $request->lookup_source
                )
            );

            return redirect()->route('admin.user-management')->with('success', 'Lookup user added to the Admin Hub successfully.');
        }

        $user = User::query()->where('email', $baseEmail)->first();

        if (!$user) {
            $studentIdSeed = trim((string) $request->input('external_identifier', ''));
            if ($studentIdSeed === '') {
                $studentIdSeed = 'faculty-' . strtolower(substr(md5($baseEmail), 0, 10));
            }

            $user = new User();
            $user->student_id = $this->resolveUniqueLocalIdentifier($studentIdSeed);
            $user->first_name = $firstName !== '' ? $firstName : 'Faculty';
            $user->last_name = $lastName !== '' ? $lastName : 'User';
            $user->name = $fullName !== '' ? $fullName : trim($user->first_name . ' ' . $user->last_name);
            $user->email = $baseEmail;
            $user->password = bcrypt(\Illuminate\Support\Str::random(40));
        }

        $user->user_role = $normalizedRequestedRole;
        if (Schema::hasColumn('users', 'status')) {
            $user->status = $request->status;
        }
        $user->save();

        $linkedAdmin = $this->findLinkedAdminProfile($user) ?? new Admin();

        if (in_array($normalizedRequestedRole, [User::ROLE_ADMIN, User::ROLE_SUPERADMIN], true)) {
            if (Admin::hasColumn('user_id')) {
                $linkedAdmin->user_id = $user->id;
            }
            if (Admin::hasColumn('first_name')) {
                $linkedAdmin->first_name = $user->first_name;
            }
            if (Admin::hasColumn('last_name')) {
                $linkedAdmin->last_name = $user->last_name;
            }
            if (Admin::hasColumn('name')) {
                $linkedAdmin->name = $user->name;
            }
            if (Admin::hasColumn('email')) {
                $linkedAdmin->email = $usesSeparateAdminEmail && trim((string) $request->input('admin_email', '')) !== ''
                    ? trim((string) $request->input('admin_email', ''))
                    : $user->email;
            }
            if (Admin::hasColumn('email_address')) {
                $linkedAdmin->email_address = $usesSeparateAdminEmail && trim((string) $request->input('admin_email', '')) !== ''
                    ? trim((string) $request->input('admin_email', ''))
                    : $user->email;
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
        }

        $this->logUserManagementAction(
            'Added user from lookup',
            sprintf(
                'Added %s (%s) from %s lookup into clinic access as %s.',
                $user->name ?? $user->email,
                $user->email,
                $request->lookup_source,
                $user->user_role
            )
        );

        return redirect()->route('admin.user-management')->with('success', 'Lookup user added to the clinic system successfully.');
    }

    public function updateAdminHub(Request $request, Admin $admin)
    {
        $this->ensureCanManageUsers();

        $request->validate([
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'admin_email' => ['required', 'email', 'max:255'],
            'office' => ['nullable', 'string', 'max:255'],
            'first_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
            'full_name' => ['nullable', 'string', 'max:255'],
        ]);

        if (Admin::hasColumn('first_name')) {
            $admin->first_name = trim((string) $request->input('first_name', ''));
        }
        if (Admin::hasColumn('last_name')) {
            $admin->last_name = trim((string) $request->input('last_name', ''));
        }
        if (Admin::hasColumn('name')) {
            $fullName = trim((string) $request->input('full_name', ''));
            $admin->name = $fullName !== '' ? $fullName : trim(implode(' ', array_filter([$admin->first_name, $admin->last_name])));
        }
        if (Admin::hasColumn('email')) {
            $admin->email = trim((string) $request->input('admin_email'));
        }
        if (Admin::hasColumn('email_address')) {
            $admin->email_address = trim((string) $request->input('admin_email'));
        }
        if (Admin::hasColumn('external_identifier')) {
            $incomingIdentifier = trim((string) $request->input('external_identifier', ''));
            if ($incomingIdentifier !== '') {
                $admin->external_identifier = $incomingIdentifier;
            }
        }
        if (Admin::hasColumn('status')) {
            $admin->status = $request->status;
        }
        if (Admin::hasColumn('office')) {
            $admin->office = $request->input('office');
        }
        if (Admin::hasColumn('access_level')) {
            $admin->access_level = 'designee';
        }
        $admin->save();

        $this->logUserManagementAction(
            'Updated admin hub profile',
            sprintf(
                'Updated admin hub record #%s (%s).',
                $admin->admin_id,
                $admin->name ?? ($admin->email ?? 'Unknown Admin')
            )
        );

        return redirect()->back()->with('success', 'Admin Hub profile updated successfully.');
    }

    public function destroyAdminHub(Admin $admin)
    {
        $this->ensureCanManageUsers();

        if (Admin::hasColumn('access_level')) {
            $admin->access_level = null;
        }
        if (Admin::hasColumn('status')) {
            $admin->status = 'active';
        }
        $admin->save();

        $this->logUserManagementAction(
            'Removed admin hub access',
            sprintf(
                'Removed admin hub designee access for record #%s (%s).',
                $admin->admin_id,
                $admin->name ?? ($admin->email ?? 'Unknown Admin')
            )
        );

        return redirect()->back()->with('success', 'Admin Hub access removed successfully.');
    }

    public function deleteAdminHubRecord(Admin $admin)
    {
        $this->ensureCanManageUsers();

        $adminName = $admin->name ?? ($admin->email ?? 'Unknown Admin');
        $adminId = $admin->admin_id;

        $admin->delete();

        $this->logUserManagementAction(
            'Deleted admin hub record',
            sprintf(
                'Deleted admin hub record #%s (%s) from the admins table.',
                $adminId,
                $adminName
            )
        );

        return redirect()->back()->with('success', 'Admin Hub record deleted successfully.');
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
                $facultyIdentifier = (string) ($faculty['faculty_id'] ?? $faculty['faculty_code'] ?? $faculty['id'] ?? '');
                $recordId = $facultyIdentifier !== '' ? $facultyIdentifier : ($email !== '' ? $email : 'faculty');

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
                    'student_id' => $facultyIdentifier,
                    'email' => $email,
                    'role' => $role !== '' ? $role : 'Faculty',
                    'raw_role' => $role,
                    'status' => $status,
                    'avatar_url' => null,
                    'avatar_letter' => strtoupper(substr($name !== '' ? $name : ($email ?: 'F'), 0, 1)),
                    'can_edit' => false,
                    'can_onboard' => true,
                    'is_external' => true,
                    'meta' => [
                        'faculty_id' => $faculty['faculty_id'] ?? null,
                        'faculty_code' => $faculty['faculty_code'] ?? null,
                        'faculty_type' => $faculty['faculty_type'] ?? null,
                        'department' => $faculty['department'] ?? null,
                        'profile' => $profile,
                        'lookup_source' => 'faculty',
                        'updated_at' => $faculty['last_updated'] ?? null,
                    ],
                ];
            })
            ->values()
            ->all();
    }

    private function collectAdminHubProfiles(string $search = ''): array
    {
        if (!Schema::hasTable('admins')) {
            return [];
        }

        $query = Admin::query();

        if (Admin::hasColumn('access_level')) {
            $query->where('access_level', 'designee');
        }

        if ($search !== '') {
            $query->where(function ($builder) use ($search) {
                foreach (['admin_id', 'name', 'first_name', 'last_name', 'email', 'email_address', 'office', 'status'] as $column) {
                    if (Admin::hasColumn($column)) {
                        $builder->orWhere($column, 'like', '%' . $search . '%');
                    }
                }
            });
        }

        return $query->orderBy('name')
            ->limit(100)
            ->get()
            ->map(function (Admin $admin) {
                $linkedUser = Admin::hasColumn('user_id') && $admin->user_id ? User::find($admin->user_id) : null;
                $externalIdentifier = trim((string) ($admin->external_identifier ?? ''));
                $displayName = trim((string) ($admin->name ?? ''));
                if ($displayName === '') {
                    $displayName = trim(implode(' ', array_filter([
                        $admin->first_name ?? '',
                        $admin->last_name ?? '',
                    ])));
                }
                $email = trim((string) ($admin->email_address ?? $admin->email ?? ''));
                $status = strtolower(trim((string) ($admin->status ?? 'active')));
                if ($status === '') {
                    $status = 'active';
                }

                return [
                    'id' => (string) $admin->admin_id,
                    'record_id' => (string) $admin->admin_id,
                    'source' => 'admin',
                    'source_label' => 'Admin Hub',
                    'name' => $displayName !== '' ? $displayName : ($email !== '' ? $email : 'Admin Hub Record'),
                    'first_name' => (string) ($admin->first_name ?? ''),
                    'last_name' => (string) ($admin->last_name ?? ''),
                    'student_id' => $externalIdentifier !== '' ? $externalIdentifier : (string) ($linkedUser?->student_id ?? ''),
                    'email' => $email,
                    'role' => 'Admin - Designee',
                    'raw_role' => 'admin',
                    'normalized_role' => User::ROLE_ADMIN,
                    'status' => $status === 'inactive' ? 'inactive' : 'active',
                    'avatar_url' => null,
                    'avatar_letter' => strtoupper(substr($displayName !== '' ? $displayName : ($email ?: 'A'), 0, 1)),
                    'can_edit' => true,
                    'is_external' => false,
                    'update_url' => route('admin.user-management.admin-hub.update', $admin->admin_id),
                    'delete_url' => route('admin.user-management.admin-hub.destroy', $admin->admin_id),
                    'delete_admin_hub_url' => route('admin.user-management.admin-hub.delete-record', $admin->admin_id),
                    'meta' => [
                        'email' => $email,
                        'access_level' => 'designee',
                        'admin_login_email' => $email,
                        'admin_profile_id' => $admin->admin_id,
                        'admin_profile_name' => $displayName,
                        'external_identifier' => $externalIdentifier,
                        'office' => (string) ($admin->office ?? ''),
                        'lookup_source' => 'admin-hub',
                        'updated_at' => optional($admin->updated_at)->toIso8601String(),
                        'linked_user_id' => $admin->user_id ?? null,
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

    private function resolveUniqueLocalIdentifier(string $seed): string
    {
        $base = trim($seed) !== '' ? trim($seed) : 'lookup-user';
        $candidate = $base;
        $counter = 1;

        while (User::query()->where('student_id', $candidate)->exists()) {
            $candidate = $base . '-' . $counter;
            $counter++;
        }

        return $candidate;
    }
}
