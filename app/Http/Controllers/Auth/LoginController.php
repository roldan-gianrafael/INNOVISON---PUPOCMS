<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Support\Facades\Auth;
use App\Models\ActivityLog;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    private function recordAuthEvent(
        Request $request,
        string $action,
        string $description,
        ?User $actor = null,
        int $statusCode = 200,
        string $eventType = 'auth'
    ): void
    {
        $user = $actor ?? Auth::user();
        $email = trim((string) $request->input('email', ''));

        if (!$user && $email === '') {
            return;
        }

        ActivityLog::create([
            'user_id' => $user?->id,
            'user_name' => $user?->name ?? $user?->email ?? $email,
            'user_role' => $user ? strtolower((string) ($user->user_role ?? '')) : null,
            'action' => $action,
            'module' => 'Authentication',
            'event_type' => $eventType,
            'description' => $description,
            'route_name' => optional($request->route())->getName(),
            'http_method' => strtoupper((string) $request->method()),
            'request_path' => '/' . ltrim((string) $request->path(), '/'),
            'status_code' => $statusCode,
            'subject_type' => 'user',
            'subject_id' => $user?->id ? (string) $user->id : null,
            'metadata' => [
                'session_id' => $request->session()->getId(),
                'email' => $email !== '' ? $email : null,
            ],
            'ip_address' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 255),
        ]);
    }

    public function login(Request $request) 
{
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);


    $user = \App\Models\User::where('email', $request->email)->first();

    if (!$user) {
        $this->recordAuthEvent(
            $request,
            'Login Failed',
            'Login attempt failed because email is not registered.',
            null,
            404,
            'error'
        );

        return back()->withErrors([
            'email' => 'This email is not registered in our system.',
        ])->withInput();
    }

  
    if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
        $request->session()->regenerate();
        $request->session()->flash('show_terms_modal', true);
        $this->recordAuthEvent($request, 'Login', 'User logged in successfully.');

        /** @var \App\Models\User $authenticatedUser */
        $authenticatedUser = Auth::user();
        $originalRole = strtolower((string) ($authenticatedUser->user_role ?? ''));
        $normalizedRole = User::normalizeRole($authenticatedUser->user_role);

        if ($normalizedRole !== $originalRole) {
            $authenticatedUser->user_role = $normalizedRole;
            $authenticatedUser->save();
        }

        if ($normalizedRole === User::ROLE_SUPER_ADMIN) {
            return redirect('/admin/dashboard');
        }
        if ($normalizedRole === User::ROLE_STUDENT_ASSISTANT) {
            return redirect('/assistant/dashboard');
        }
        return redirect('/student/home');
    }

    $this->recordAuthEvent(
        $request,
        'Login Failed',
        'Login attempt failed because password is incorrect.',
        $user,
        401,
        'error'
    );

    return back()->withErrors([
        'password' => 'Incorrect password. Please try again.',
    ])->withInput();
}

public function logout(Request $request)
{
    if (Auth::check()) {
        $this->recordAuthEvent($request, 'Logout', 'User logged out from the system.');
    }

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
