<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class RegisterController extends Controller
{
    public function register(Request $request) {
    if ((bool) config('services.idp.enabled', false)) {
        abort(403, 'Local registration is disabled while centralized login is enabled.');
    }

    $request->validate([
        'first_name' => 'required|string|max:255',
        'last_name'  => 'required|string|max:255',
        'student_id' => 'required|unique:users',
        'email'      => 'required|email|unique:users',
        'DOB'        => 'required|date',
        'course'     => 'required',
        'year'       => 'required',
        'section'    => 'required',
        'password'   => 'required|min:6|confirmed',
    ]);

    $payload = [
        'first_name' => $request->first_name,
        'last_name'  => $request->last_name,
        'name'       => $request->first_name . ' ' . $request->last_name, // Automated concatenation
        'student_id' => $request->student_id,
        'email'      => $request->email,
        'DOB'        => $request->DOB,
        'course'     => $request->course,
        'year'       => $request->year,
        'section'    => $request->section,
        'user_role'  => 'student', // Default role
        'password'   => Hash::make($request->password),
    ];

    if (Schema::hasColumn('users', 'user_type')) {
        $payload['user_type'] = 'Regular'; // Default type
    }

    $user = User::create($payload);

    Auth::login($user);
    return redirect('/student/home');
}
}
