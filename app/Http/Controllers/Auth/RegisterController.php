<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class RegisterController extends Controller
{
    public function register(Request $request) {
    abort_unless(app()->environment('local'), 404);

    if ((bool) config('services.idp.enabled', false)) {
        abort(403, 'Local registration is disabled while centralized login is enabled.');
    }

    $request->validate([
        'first_name' => 'required|string|max:255',
        'middle_name' => 'nullable|string|max:255',
        'last_name'  => 'required|string|max:255',
        'email'      => 'required|email|unique:users',
        'clinic_role' => 'required|string|in:admin_clinic_staff,student_assistant,super_admin',
        'password'   => 'required|min:6|confirmed',
    ]);

    $selectedRole = (string) $request->input('clinic_role');
    $isStudentAssistant = $selectedRole === 'student_assistant';
    $userRole = $selectedRole === 'super_admin'
        ? User::ROLE_SUPERADMIN
        : User::ROLE_ADMIN;

    $studentId = 'LOC-' . strtoupper(Str::random(10));
    while (User::where('student_id', $studentId)->exists()) {
        $studentId = 'LOC-' . strtoupper(Str::random(10));
    }

    $payload = [
        'first_name' => $request->first_name,
        'middle_name' => $request->input('middle_name'),
        'last_name'  => $request->last_name,
        'name'       => trim(implode(' ', array_filter([
            $request->first_name,
            $request->input('middle_name'),
            $request->last_name,
        ]))),
        'student_id' => $studentId,
        'email'      => $request->email,
        'DOB'        => null,
        'course'     => null,
        'year'       => null,
        'section'    => null,
        'user_role'  => $userRole,
        'status'     => 'active',
        'password'   => Hash::make($request->password),
    ];

    if (Schema::hasColumn('users', 'user_type')) {
        $payload['user_type'] = $isStudentAssistant ? 'Assistant' : 'Regular';
    }

    $user = User::create($payload);

    Auth::shouldUse('admin');
    Auth::guard('admin')->login($user);
    $request->session()->regenerate();

    if ($userRole === User::ROLE_SUPERADMIN) {
        return redirect('/admin/dashboard');
    }

    if ($isStudentAssistant) {
        return redirect('/assistant/choose-portal');
    }

    return redirect('/assistant/dashboard');
}
}
