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


    $user = \App\Models\User::where('email', $request->email)->first();

    if (!$user) {

        return back()->withErrors([
            'email' => 'This email is not registered in our system.',
        ])->withInput();
    }

  
    if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
        $request->session()->regenerate();
        
        if (Auth::user()->user_role === 'admin') {
            return redirect('/admin/dashboard');
        }
        if ($user->user_role === 'student_assistant') {
            
        return redirect('/admin/dashboard');
        }
        return redirect('/student/home');
    }

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

    return view('login'); 
}
}
