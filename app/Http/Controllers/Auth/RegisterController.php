<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    public function register(Request $request) {
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

    $user = User::create([
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
        'user_type'  => 'Regular', // Default type
        'password'   => Hash::make($request->password),
    ]);

    Auth::login($user);
    return redirect('/student/home');
}
}