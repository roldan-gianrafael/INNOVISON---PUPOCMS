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
    if ((bool) config('services.idp.enabled', false)) {
        abort(403, 'Local registration is disabled while centralized login is enabled.');
    }

    $request->validate([
        'first_name' => 'required|string|max:255',
        'last_name'  => 'required|string|max:255',
        'email'      => 'required|email|unique:users',
        'course'     => 'required',
        'password'   => 'required|min:6|confirmed',
    ]);

    $studentId = 'LOC-' . strtoupper(Str::random(10));
    while (User::where('student_id', $studentId)->exists()) {
        $studentId = 'LOC-' . strtoupper(Str::random(10));
    }

    $payload = [
        'first_name' => $request->first_name,
        'last_name'  => $request->last_name,
        'name'       => $request->first_name . ' ' . $request->last_name, // Automated concatenation
        'student_id' => $studentId,
        'email'      => $request->email,
        'DOB'        => null,
        'course'     => $request->course,
        'year'       => null,
        'section'    => null,
        'user_role'  => 'student', // Default role
        'password'   => Hash::make($request->password),
    ];

    if (Schema::hasColumn('users', 'user_type')) {
        $payload['user_type'] = 'Regular'; // Default type
    }

    $user = User::create($payload);

    Auth::guard('student')->login($user);
    $request->session()->regenerate();
    return redirect('/student/home');
}
}
