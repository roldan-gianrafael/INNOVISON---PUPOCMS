<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function login(Request $request) 
{
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    // 1. Hanapin muna kung may ganitong email sa database
    $user = \App\Models\User::where('email', $request->email)->first();

    if (!$user) {
        // Kung walang nahanap na email
        return back()->withErrors([
            'email' => 'This email is not registered in our system.',
        ])->withInput();
    }

    // 2. Kung nandun ang email, i-check naman ang password
    if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
        $request->session()->regenerate();
        
        if (Auth::user()->user_role === 'admin') {
            return redirect('/admin/dashboard');
        }
        return redirect('/student/home');
    }

    // 3. Kung nakarating dito, ibig sabihin tama ang email pero MALI ANG PASSWORD
    return back()->withErrors([
        'password' => 'Incorrect password. Please try again.',
    ])->withInput();
}

public function logout(Request $request)
{
    Auth::logout();

    $request->session()->invalidate(); 
    $request->session()->regenerateToken(); 

    return redirect('/login'); 
}
public function showLoginForm()
{
    // Dahil nasa views/student/login.blade.php ang file mo
    return view('login'); 
}
}
