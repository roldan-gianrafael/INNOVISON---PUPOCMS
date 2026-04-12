<?php

namespace App\Http\Middleware;

use App\Models\Admin;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles)
    {
        if (!Auth::check()) {
            abort(403, 'Unauthorized');
        }

        $user = Auth::user();
        $currentRole = User::normalizeRole(Auth::user()->user_role ?? '');
        $allowedRoles = array_map(function ($role) {
            return User::normalizeRole((string) $role);
        }, $roles);

        if ($this->hasRoleAccess($user, $currentRole, $allowedRoles)) {
            return $next($request);
        }

        abort(403, 'Unauthorized');
    }

    private function hasRoleAccess($user, string $currentRole, array $allowedRoles): bool
    {
        if (in_array($currentRole, $allowedRoles, true)) {
            if ($currentRole === User::ROLE_ADMIN && in_array(User::ROLE_ADMIN, $allowedRoles, true)) {
                return $this->isClinicStaffAdmin($user);
            }

            return true;
        }

        if (in_array(User::ROLE_STUDENT, $allowedRoles, true) && $this->isDesigneeAdmin($user)) {
            return true;
        }

        return false;
    }

    private function isDesigneeAdmin($user): bool
    {
        if (!$user || User::normalizeRole((string) ($user->user_role ?? '')) !== User::ROLE_ADMIN) {
            return false;
        }

        $linkedAdmin = $this->findLinkedAdminProfile($user);
        $accessLevel = strtolower(trim((string) ($linkedAdmin?->access_level ?? '')));

        return $accessLevel === 'designee';
    }

    private function isClinicStaffAdmin($user): bool
    {
        if (!$user || User::normalizeRole((string) ($user->user_role ?? '')) !== User::ROLE_ADMIN) {
            return false;
        }

        $linkedAdmin = $this->findLinkedAdminProfile($user);
        $accessLevel = strtolower(trim((string) ($linkedAdmin?->access_level ?? '')));

        return in_array($accessLevel, ['clinic_staff', 'clinic staff', 'staff'], true);
    }

    private function findLinkedAdminProfile($user): ?Admin
    {
        if (!Schema::hasTable('admins')) {
            return null;
        }

        $email = trim((string) ($user->email ?? ''));
        if ($email === '') {
            return null;
        }

        return Admin::query()
            ->where(function ($builder) use ($email) {
                if (Admin::hasColumn('email')) {
                    $builder->orWhere('email', $email);
                }

                if (Admin::hasColumn('email_address')) {
                    $builder->orWhere('email_address', $email);
                }
            })
            ->first();
    }
}
