<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class EmergencyAuthController extends Controller
{
    public function showLoginForm(): RedirectResponse|\Illuminate\View\View
    {
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.dashboard');
        }

        return view('auth.emergency-login');
    }

    public function login(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string', 'max:255'],
        ]);

        $email = strtolower(trim((string) $validated['email']));
        $password = (string) $validated['password'];
        $guard = Auth::guard('admin');

        if (!$guard->attempt(['email' => $email, 'password' => $password], false)) {
            $this->logAttempt($request, null, 'Emergency login failed because the email or password was invalid.', 401);

            return back()
                ->withErrors(['email' => 'Invalid emergency credentials.'])
                ->withInput($request->only('email'));
        }

        $request->session()->regenerate();
        Auth::shouldUse('admin');

        /** @var \App\Models\User|null $user */
        $user = $guard->user();
        if (!$user) {
            $guard->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()->withErrors(['email' => 'Emergency login could not complete.'])->withInput($request->only('email'));
        }

        $normalizedRole = User::normalizeRole((string) ($user->user_role ?? ''));
        if (!in_array($normalizedRole, [User::ROLE_ADMIN, User::ROLE_SUPERADMIN], true)) {
            $guard->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            $this->logAttempt($request, $user, 'Emergency login blocked because the account is not an admin or nurse account.', 403);

            return back()->withErrors([
                'email' => 'This backdoor is limited to clinic admin and nurse accounts.',
            ])->withInput($request->only('email'));
        }

        if (strtolower(trim((string) ($user->status ?? 'active'))) === 'inactive') {
            $guard->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            $this->logAttempt($request, $user, 'Emergency login blocked because the account is inactive.', 423);

            return back()->withErrors([
                'email' => 'This account is inactive.',
            ])->withInput($request->only('email'));
        }

        $this->logAttempt($request, $user, 'Emergency login succeeded.', 200);

        return redirect()
            ->route($normalizedRole === User::ROLE_SUPERADMIN ? 'admin.dashboard' : 'assistant.dashboard')
            ->with('success', 'Emergency backup login successful.');
    }

    private function logAttempt(Request $request, ?User $user, string $description, int $statusCode): void
    {
        try {
            ActivityLog::create([
                'user_id' => $user?->id,
                'user_name' => $user?->name ?? $request->input('email'),
                'user_role' => $user ? strtolower((string) ($user->user_role ?? '')) : null,
                'action' => 'Emergency Login',
                'module' => 'Authentication',
                'event_type' => $statusCode === 200 ? 'auth' : 'error',
                'description' => $description,
                'route_name' => 'system-admin.emergency-login',
                'http_method' => strtoupper($request->method()),
                'request_path' => '/' . ltrim($request->path(), '/'),
                'status_code' => $statusCode,
                'subject_type' => 'user',
                'subject_id' => $user?->id ? (string) $user->id : null,
                'metadata' => [
                    'email' => $request->input('email'),
                    'emergency_login' => true,
                ],
                'ip_address' => $request->ip(),
                'user_agent' => substr((string) $request->userAgent(), 0, 255),
            ]);
        } catch (\Throwable $exception) {
            Log::warning('Emergency login audit log could not be written.', [
                'message' => $exception->getMessage(),
            ]);
        }
    }
}
