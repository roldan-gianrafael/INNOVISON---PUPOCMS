<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    private function usersTableHasUserTypeColumn(): bool
    {
        static $hasColumn;

        if ($hasColumn === null) {
            $hasColumn = Schema::hasColumn('users', 'user_type');
        }

        return $hasColumn;
    }

    private function studentRoleValue(): string
    {
        if (defined(User::class . '::ROLE_STUDENT')) {
            return (string) constant(User::class . '::ROLE_STUDENT');
        }

        return 'student';
    }

    private function adminRoleValue(): string
    {
        if (defined(User::class . '::ROLE_ADMIN')) {
            return (string) constant(User::class . '::ROLE_ADMIN');
        }

        if (defined(User::class . '::ROLE_STUDENT_ASSISTANT')) {
            return (string) constant(User::class . '::ROLE_STUDENT_ASSISTANT');
        }

        return 'admin';
    }

    private function superAdminRoleValue(): string
    {
        if (defined(User::class . '::ROLE_SUPERADMIN')) {
            return (string) constant(User::class . '::ROLE_SUPERADMIN');
        }

        if (defined(User::class . '::ROLE_SUPER_ADMIN')) {
            return (string) constant(User::class . '::ROLE_SUPER_ADMIN');
        }

        return 'superadmin';
    }

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

    private function useIdpAuth(): bool
    {
        return (bool) config('services.idp.enabled', false);
    }

    private function redirectPathByRole(string $role): string
    {
        $normalizedRole = User::normalizeRole($role);

        if ($normalizedRole === User::normalizeRole($this->superAdminRoleValue())) {
            return '/admin/dashboard';
        }

        if ($normalizedRole === User::normalizeRole($this->adminRoleValue())) {
            return '/assistant/dashboard';
        }

        return '/student/home';
    }

    private function idpUrl(string $path): ?string
    {
        $baseUrl = trim((string) config('services.idp.base_url', ''));
        if ($baseUrl === '') {
            return null;
        }

        if (Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }

        return rtrim($baseUrl, '/') . '/' . ltrim($path, '/');
    }

    private function buildAuthorizeUrl(): ?string
    {
        $clientId = trim((string) config('services.idp.client_id', ''));
        if ($clientId === '') {
            return null;
        }

        $authorizePath = trim((string) config('services.idp.authorize_path', '/auth/authorize'));
        $authorizeUrl = $this->idpUrl($authorizePath);
        if ($authorizeUrl === null) {
            return null;
        }

        $query = [
            'client_id' => $clientId,
        ];

        $redirectUri = trim((string) config('services.idp.redirect_uri', ''));
        $includeRedirectUri = (bool) config('services.idp.authorize_include_redirect_uri', false);
        if ($includeRedirectUri && $redirectUri !== '') {
            $query['redirect_uri'] = $redirectUri;
        }

        $responseType = trim((string) config('services.idp.authorize_response_type', 'code'));
        if ($responseType !== '') {
            $query['response_type'] = $responseType;
        }

        $scope = trim((string) config('services.idp.authorize_scope', ''));
        if ($scope !== '') {
            $query['scope'] = $scope;
        }

        return $authorizeUrl . '?' . http_build_query($query);
    }

    public function debugAuthorizeUrl(Request $request)
    {
        if (!config('app.debug')) {
            abort(404);
        }

        return response()->json([
            'authorize_url' => $this->buildAuthorizeUrl(),
            'idp_enabled' => $this->useIdpAuth(),
            'redirect_uri' => config('services.idp.redirect_uri'),
            'authorize_path' => config('services.idp.authorize_path'),
            'response_type' => config('services.idp.authorize_response_type'),
        ]);
    }

    private function normalizeCookieSameSite(): string
    {
        $sameSite = strtolower(trim((string) config('services.idp.cookie_same_site', 'lax')));
        if (!in_array($sameSite, ['lax', 'strict', 'none'], true)) {
            return 'lax';
        }

        return $sameSite;
    }

    private function attachIdpCookies(RedirectResponse $response, ?string $accessToken, ?string $refreshToken): RedirectResponse
    {
        $secure = (bool) config('services.idp.cookie_secure', true);
        $sameSite = $this->normalizeCookieSameSite();

        $accessCookieName = trim((string) config('services.idp.access_cookie_name', 'access_token'));
        if ($accessCookieName !== '' && $accessToken !== null && $accessToken !== '') {
            $response->cookie(
                $accessCookieName,
                $accessToken,
                (int) config('services.idp.access_cookie_minutes', 60),
                '/',
                null,
                $secure,
                true,
                false,
                $sameSite
            );
        }

        $refreshCookieName = trim((string) config('services.idp.refresh_cookie_name', 'refresh_token'));
        if ($refreshCookieName !== '' && $refreshToken !== null && $refreshToken !== '') {
            $response->cookie(
                $refreshCookieName,
                $refreshToken,
                (int) config('services.idp.refresh_cookie_minutes', 10080),
                '/',
                null,
                $secure,
                true,
                false,
                $sameSite
            );
        }

        return $response;
    }

    private function clearIdpCookies(RedirectResponse $response): RedirectResponse
    {
        $secure = (bool) config('services.idp.cookie_secure', true);
        $sameSite = $this->normalizeCookieSameSite();

        foreach (['access_cookie_name', 'refresh_cookie_name'] as $cookieKey) {
            $cookieName = trim((string) config('services.idp.' . $cookieKey, ''));
            if ($cookieName === '') {
                continue;
            }

            $response->cookie(
                $cookieName,
                '',
                -60,
                '/',
                null,
                $secure,
                true,
                false,
                $sameSite
            );
        }

        return $response;
    }

    private function exchangeCodeForTokens(string $code): ?array
    {
        $tokenPath = trim((string) config('services.idp.token_path', '/auth/token'));
        $tokenUrl = $this->idpUrl($tokenPath);
        if ($tokenUrl === null) {
            return null;
        }

        $clientId = trim((string) config('services.idp.client_id', ''));
        $clientSecret = trim((string) config('services.idp.client_secret', ''));
        $payload = [
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'code' => $code,
        ];

        $redirectUri = trim((string) config('services.idp.redirect_uri', ''));
        $includeRedirectUri = (bool) config('services.idp.token_include_redirect_uri', false);
        if ($includeRedirectUri && $redirectUri !== '') {
            $payload['redirect_uri'] = $redirectUri;
        }

        $grantType = trim((string) config('services.idp.token_grant_type', 'authorization_code'));
        if ($grantType === '') {
            $grantType = 'authorization_code';
        }
        $payload['grant_type'] = $grantType;

        if ($clientId === '' || $clientSecret === '') {
            return null;
        }

        $authMethod = $this->idpTokenAuthMethod();
        $response = $this->performTokenExchangeRequest($tokenUrl, $payload, $clientId, $clientSecret, $authMethod);
        if ($response->successful() && is_array($response->json())) {
            return $response->json();
        }

        Log::warning('IDP token exchange failed.', [
            'token_url' => $tokenUrl,
            'auth_method' => $authMethod,
            'status' => $response->status(),
            'body' => Str::limit((string) $response->body(), 1200),
            'payload_flags' => [
                'has_code' => isset($payload['code']) && $payload['code'] !== '',
                'has_grant_type' => isset($payload['grant_type']) && $payload['grant_type'] !== '',
                'has_redirect_uri' => isset($payload['redirect_uri']) && $payload['redirect_uri'] !== '',
            ],
        ]);

        return null;
    }

    private function idpTokenAuthMethod(): string
    {
        $method = strtolower(trim((string) config('services.idp.token_auth_method', 'client_secret_post')));

        if (!in_array($method, ['client_secret_post', 'client_secret_basic', 'json'], true)) {
            return 'client_secret_post';
        }

        return $method;
    }

    private function performTokenExchangeRequest(
        string $tokenUrl,
        array $payload,
        string $clientId,
        string $clientSecret,
        string $authMethod
    ) {
        $request = Http::acceptJson()->timeout(20);

        if ($authMethod === 'client_secret_basic') {
            $basicPayload = $payload;
            unset($basicPayload['client_secret']);

            return $request
                ->withBasicAuth($clientId, $clientSecret)
                ->asForm()
                ->post($tokenUrl, $basicPayload);
        }

        if ($authMethod === 'json') {
            return $request->post($tokenUrl, $payload);
        }

        return $request
            ->asForm()
            ->post($tokenUrl, $payload);
    }

    private function hasIdentityFields(array $payload): bool
    {
        foreach (['email', 'role', 'roles', 'user_role', 'student_number', 'student_id', 'name', 'first_name', 'last_name', 'user_id', 'id'] as $key) {
            $value = data_get($payload, $key);
            if (is_string($value) && trim($value) !== '') {
                return true;
            }

            if (is_numeric($value)) {
                return true;
            }

            if (is_array($value) && !empty($value)) {
                return true;
            }
        }

        return false;
    }

    private function extractProfilePayload(array $payload): ?array
    {
        $candidates = [
            data_get($payload, 'user'),
            data_get($payload, 'profile'),
            data_get($payload, 'data.user'),
            data_get($payload, 'data.profile'),
            data_get($payload, 'data'),
            $payload,
        ];

        foreach ($candidates as $candidate) {
            if (!is_array($candidate)) {
                continue;
            }

            if ($this->hasIdentityFields($candidate)) {
                return $candidate;
            }
        }

        return null;
    }

    private function fetchProfileFromIdp(string $accessToken): ?array
    {
        $profilePaths = (array) config('services.idp.profile_paths', []);
        $validateTokenPath = trim((string) config('services.idp.validate_token_path', ''));

        foreach ($profilePaths as $path) {
            $path = trim((string) $path);
            if ($path === '') {
                continue;
            }

            $url = $this->idpUrl($path);
            if ($url === null) {
                continue;
            }

            $response = Http::acceptJson()->timeout(20)->withToken($accessToken)->get($url);
            if (!$response->successful() || !is_array($response->json())) {
                continue;
            }

            $profile = $this->extractProfilePayload($response->json());
            if ($profile !== null) {
                return $profile;
            }
        }

        if ($validateTokenPath !== '') {
            $validateUrl = $this->idpUrl($validateTokenPath);
            if ($validateUrl !== null) {
                $response = Http::acceptJson()->timeout(20)->post($validateUrl, [
                    'token' => $accessToken,
                ]);

                if ($response->successful() && is_array($response->json())) {
                    $profile = $this->extractProfilePayload($response->json());
                    if ($profile !== null) {
                        return $profile;
                    }
                }
            }
        }

        return null;
    }

    private function extractRawRoles(array $profile): array
    {
        $sources = [
            data_get($profile, 'role'),
            data_get($profile, 'user_role'),
            data_get($profile, 'roles'),
            data_get($profile, 'authorities'),
            data_get($profile, 'data.role'),
            data_get($profile, 'data.roles'),
            data_get($profile, 'data.authorities'),
        ];

        $roles = [];
        foreach ($sources as $source) {
            if (is_string($source)) {
                foreach (explode(',', $source) as $role) {
                    $trimmed = trim($role);
                    if ($trimmed !== '') {
                        $roles[] = $trimmed;
                    }
                }
                continue;
            }

            if (is_array($source)) {
                foreach ($source as $role) {
                    if (is_string($role) && trim($role) !== '') {
                        $roles[] = trim($role);
                    }
                }
            }
        }

        return array_values(array_unique($roles));
    }

    private function configuredIdpRolePrefix(): string
    {
        $rolePrefix = strtolower(trim((string) config('services.idp.role_prefix', 'OCMS:')));
        if ($rolePrefix !== '' && !Str::endsWith($rolePrefix, ':')) {
            $rolePrefix .= ':';
        }

        return $rolePrefix;
    }

    private function parseIdpRoleToken(string $role): array
    {
        $normalized = strtolower(trim((string) $role));
        if ($normalized === '') {
            return ['', false];
        }

        $isScoped = false;
        $rolePrefix = $this->configuredIdpRolePrefix();
        if ($rolePrefix !== '' && Str::startsWith($normalized, $rolePrefix)) {
            $normalized = substr($normalized, strlen($rolePrefix));
            $isScoped = true;
        } elseif (Str::startsWith($normalized, 'ocms:')) {
            $normalized = substr($normalized, 5);
            $isScoped = true;
        }

        return [trim($normalized), $isScoped];
    }

    private function normalizeIdpRoleToken(string $role): string
    {
        [$normalized] = $this->parseIdpRoleToken($role);
        return $normalized;
    }

    private function mapSingleIdpRoleToLocal(string $role): ?string
    {
        $normalized = $this->normalizeIdpRoleToken($role);
        if ($normalized === '') {
            return null;
        }

        if (in_array($normalized, ['superadmin', 'super_admin'], true)) {
            return $this->superAdminRoleValue();
        }

        if (in_array($normalized, ['admin', 'student_assistant', 'assistant', 'studentassistant'], true)) {
            return $this->adminRoleValue();
        }

        if ($normalized === 'student') {
            return $this->studentRoleValue();
        }

        return null;
    }

    private function resolveLocalRoleFromTokens(array $normalizedRoles): ?string
    {
        $normalizedRoles = array_values(array_unique(array_filter(array_map('trim', $normalizedRoles))));
        if (empty($normalizedRoles)) {
            return null;
        }

        $hasAdmin = count(array_intersect($normalizedRoles, ['admin', 'student_assistant', 'assistant', 'studentassistant'])) > 0;
        if ($hasAdmin) {
            return $this->adminRoleValue();
        }

        if (in_array('student', $normalizedRoles, true)) {
            return $this->studentRoleValue();
        }

        if (in_array('superadmin', $normalizedRoles, true) || in_array('super_admin', $normalizedRoles, true)) {
            return $this->superAdminRoleValue();
        }

        return null;
    }

    private function mapIdpRolesToLocal(array $roles, ?string $preferredRole = null): string
    {
        $normalizedRoles = [];
        $scopedRoles = [];

        foreach ($roles as $role) {
            [$normalized, $isScoped] = $this->parseIdpRoleToken((string) $role);
            if ($normalized === '') {
                continue;
            }

            $normalizedRoles[] = $normalized;
            if ($isScoped) {
                $scopedRoles[] = $normalized;
            }
        }

        // Least-privilege: when OCMS-scoped roles are present, ignore unscoped/global roles.
        $scopedMappedRole = $this->resolveLocalRoleFromTokens($scopedRoles);
        if ($scopedMappedRole !== null) {
            return $scopedMappedRole;
        }

        if ($preferredRole !== null) {
            [, $preferredIsScoped] = $this->parseIdpRoleToken($preferredRole);
            if ($preferredIsScoped) {
                $preferredMappedRole = $this->mapSingleIdpRoleToLocal($preferredRole);
                if ($preferredMappedRole !== null) {
                    return $preferredMappedRole;
                }
            }
        }

        $mappedFromRoles = $this->resolveLocalRoleFromTokens($normalizedRoles);
        if ($mappedFromRoles !== null) {
            return $mappedFromRoles;
        }

        if ($preferredRole !== null) {
            $preferredMappedRole = $this->mapSingleIdpRoleToLocal($preferredRole);
            if ($preferredMappedRole !== null) {
                return $preferredMappedRole;
            }
        }

        return $this->studentRoleValue();
    }

    private function firstNonEmptyScalar(array $payload, array $keys): ?string
    {
        foreach ($keys as $key) {
            $value = data_get($payload, $key);
            if (is_string($value) && trim($value) !== '') {
                return trim($value);
            }

            if (is_numeric($value)) {
                return (string) $value;
            }
        }

        return null;
    }

    private function splitName(string $fullName): array
    {
        $clean = trim(preg_replace('/\s+/', ' ', $fullName));
        if ($clean === '') {
            return ['IDP', 'User', 'IDP User'];
        }

        $parts = explode(' ', $clean);
        if (count($parts) === 1) {
            return [$parts[0], 'User', trim($parts[0] . ' User')];
        }

        $firstName = array_shift($parts);
        $lastName = implode(' ', $parts);

        return [$firstName, $lastName, trim($firstName . ' ' . $lastName)];
    }

    private function normalizeStudentIdSeed(?string $seed): string
    {
        $value = trim((string) $seed);
        if ($value === '') {
            return 'idp-' . Str::lower(Str::random(10));
        }

        $value = preg_replace('/\s+/', '-', $value);
        $value = preg_replace('/[^A-Za-z0-9\-_]/', '', $value);

        if ($value === '') {
            return 'idp-' . Str::lower(Str::random(10));
        }

        return $value;
    }

    private function resolveUniqueStudentId(string $seed, ?int $ignoreUserId = null): string
    {
        $base = $this->normalizeStudentIdSeed($seed);
        $candidate = $base;
        $counter = 1;

        while (User::query()
            ->where('student_id', $candidate)
            ->when($ignoreUserId !== null, fn ($query) => $query->where('id', '!=', $ignoreUserId))
            ->exists()) {
            $candidate = $base . '-' . $counter;
            $counter++;
        }

        return $candidate;
    }

    private function resolveUniqueEmail(string $seed, ?int $ignoreUserId = null): string
    {
        $email = strtolower(trim($seed));
        if ($email === '' || !str_contains($email, '@')) {
            $email = 'idp-' . Str::lower(Str::random(12)) . '@idp.local';
        }

        $localPart = Str::before($email, '@');
        $domain = Str::after($email, '@');
        if ($domain === '') {
            $domain = 'idp.local';
        }

        $candidate = $localPart . '@' . $domain;
        $counter = 1;

        while (User::query()
            ->where('email', $candidate)
            ->when($ignoreUserId !== null, fn ($query) => $query->where('id', '!=', $ignoreUserId))
            ->exists()) {
            $candidate = $localPart . '+' . $counter . '@' . $domain;
            $counter++;
        }

        return $candidate;
    }

    private function upsertLocalUserFromIdpProfile(array $profile): User
    {
        $emailSeed = $this->firstNonEmptyScalar($profile, ['email', 'mail', 'username', 'user.email']) ?? '';
        $studentIdSeed = $this->firstNonEmptyScalar($profile, ['student_number', 'student_id', 'studentNo', 'user_id', 'id']) ?? '';
        $firstName = $this->firstNonEmptyScalar($profile, ['first_name', 'firstname', 'given_name']) ?? '';
        $lastName = $this->firstNonEmptyScalar($profile, ['last_name', 'lastname', 'family_name']) ?? '';
        $displayName = $this->firstNonEmptyScalar($profile, ['name', 'full_name', 'display_name']) ?? '';

        if ($displayName === '' && ($firstName !== '' || $lastName !== '')) {
            $displayName = trim($firstName . ' ' . $lastName);
        }

        if ($displayName === '' && $emailSeed !== '' && str_contains($emailSeed, '@')) {
            $displayName = Str::title(str_replace(['.', '_', '-'], ' ', (string) Str::before($emailSeed, '@')));
        }

        [$splitFirstName, $splitLastName, $fullName] = $this->splitName($displayName);
        $firstName = $firstName !== '' ? $firstName : $splitFirstName;
        $lastName = $lastName !== '' ? $lastName : $splitLastName;

        $preferredRole = $this->firstNonEmptyScalar($profile, [
            'role',
            'user_role',
            'primary_role',
            'data.role',
            'user.role',
            'data.user.role',
        ]);
        $role = $this->mapIdpRolesToLocal($this->extractRawRoles($profile), $preferredRole);

        $normalizedEmailSeed = trim(strtolower($emailSeed));
        $existingUser = null;

        if ($normalizedEmailSeed !== '' && str_contains($normalizedEmailSeed, '@')) {
            $existingUser = User::query()->where('email', $normalizedEmailSeed)->first();
        }

        if (!$existingUser && $studentIdSeed !== '') {
            $existingUser = User::query()->where('student_id', $studentIdSeed)->first();
        }

        if ($existingUser) {
            $existingUser->email = $this->resolveUniqueEmail(
                $normalizedEmailSeed !== '' ? $normalizedEmailSeed : (string) $existingUser->email,
                (int) $existingUser->id
            );
            $existingUser->first_name = $firstName !== '' ? $firstName : ($existingUser->first_name ?: 'IDP');
            $existingUser->last_name = $lastName !== '' ? $lastName : ($existingUser->last_name ?: 'User');
            $existingUser->name = trim($fullName !== '' ? $fullName : ($existingUser->first_name . ' ' . $existingUser->last_name));
            $existingUser->user_role = $role;

            $shouldUpdateStudentId = trim((string) $existingUser->student_id) === '' || Str::startsWith(strtolower((string) $existingUser->student_id), 'idp-');
            if ($studentIdSeed !== '' && $shouldUpdateStudentId) {
                $existingUser->student_id = $this->resolveUniqueStudentId($studentIdSeed, (int) $existingUser->id);
            } elseif (trim((string) $existingUser->student_id) === '') {
                $existingUser->student_id = $this->resolveUniqueStudentId('idp-' . $existingUser->id, (int) $existingUser->id);
            }

            if (trim((string) $existingUser->password) === '') {
                $existingUser->password = Hash::make(Str::random(40));
            }

            if ($this->usersTableHasUserTypeColumn() && empty($existingUser->user_type)) {
                $existingUser->user_type = User::normalizeRole($role) === User::normalizeRole($this->studentRoleValue()) ? 'Regular' : 'Assistant';
            }

            $existingUser->save();
            return $existingUser;
        }

        $studentId = $this->resolveUniqueStudentId($studentIdSeed !== '' ? $studentIdSeed : ('idp-' . Str::lower(Str::random(10))));
        $email = $this->resolveUniqueEmail(
            $normalizedEmailSeed !== '' ? $normalizedEmailSeed : ($studentId . '@idp.local')
        );

        $user = User::create([
            'student_id' => $studentId,
            'first_name' => $firstName !== '' ? $firstName : 'IDP',
            'last_name' => $lastName !== '' ? $lastName : 'User',
            'name' => $fullName !== '' ? $fullName : trim(($firstName ?: 'IDP') . ' ' . ($lastName ?: 'User')),
            'email' => $email,
            'user_role' => $role,
            'password' => Hash::make(Str::random(40)),
        ]);

        if ($this->usersTableHasUserTypeColumn() && empty($user->user_type)) {
            $user->user_type = User::normalizeRole($role) === User::normalizeRole($this->studentRoleValue()) ? 'Regular' : 'Assistant';
            $user->save();
        }

        return $user;
    }

    public function login(Request $request)
    {
        if ($this->useIdpAuth()) {
            return redirect('/login?idp_error=1')->withErrors([
                'idp' => 'Centralized login is enabled. Use the identity provider sign-in flow.',
            ]);
        }

        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

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
            $normalizedRole = User::normalizeRole($authenticatedUser->user_role);

            if ($normalizedRole !== strtolower((string) ($authenticatedUser->user_role ?? ''))) {
                $authenticatedUser->user_role = $normalizedRole;
                $authenticatedUser->save();
            }

            return redirect($this->redirectPathByRole($normalizedRole));
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

    public function handleIdpCallback(Request $request): RedirectResponse
    {
        Log::info('IDP callback reached.', [
            'has_code' => $request->query->has('code'),
            'query_keys' => array_keys($request->query()),
            'path' => $request->path(),
        ]);

        if (!$this->useIdpAuth()) {
            Log::warning('IDP callback aborted because IDP auth is disabled.');
            return redirect('/login');
        }

        $code = trim((string) $request->query('code', ''));
        if ($code === '') {
            Log::warning('IDP callback missing authorization code.');
            return redirect('/login?idp_error=1')->withErrors([
                'idp' => 'Missing authorization code from the identity provider.',
            ]);
        }

        Log::info('IDP callback received authorization code.', [
            'code_length' => strlen($code),
        ]);

        $tokenPayload = $this->exchangeCodeForTokens($code);
        if ($tokenPayload === null) {
            Log::warning('IDP callback token exchange returned no payload.');
            return redirect('/login?idp_error=1')->withErrors([
                'idp' => 'Token exchange failed. Please try signing in again.',
            ]);
        }

        $accessToken = trim((string) ($tokenPayload['access_token'] ?? ''));
        $refreshToken = trim((string) ($tokenPayload['refresh_token'] ?? ''));
        if ($accessToken === '') {
            Log::warning('IDP callback token payload did not contain an access token.', [
                'payload_keys' => array_keys($tokenPayload),
            ]);
            return redirect('/login?idp_error=1')->withErrors([
                'idp' => 'Identity provider did not return an access token.',
            ]);
        }

        Log::info('IDP callback token exchange succeeded.', [
            'has_refresh_token' => $refreshToken !== '',
            'payload_keys' => array_keys($tokenPayload),
        ]);

        // Prefer profile fetched from IDP user-info endpoints over token payload fields.
        $profile = $this->fetchProfileFromIdp($accessToken);
        if ($profile === null) {
            Log::warning('IDP profile fetch returned null; falling back to token payload.');
            $profile = $this->extractProfilePayload($tokenPayload);
        }

        if ($profile === null) {
            Log::warning('IDP callback could not resolve a profile payload.');
            return redirect('/login?idp_error=1')->withErrors([
                'idp' => 'Unable to retrieve your profile from the identity provider.',
            ]);
        }

        Log::info('IDP callback resolved profile payload.', [
            'profile_keys' => array_keys($profile),
        ]);

        $user = $this->upsertLocalUserFromIdpProfile($profile);
        $user->user_role = User::normalizeRole($user->user_role);
        $user->save();

        Log::info('IDP callback upserted local user.', [
            'user_id' => $user->id,
            'user_role' => $user->user_role,
            'email' => $user->email,
        ]);

        Auth::login($user);
        $request->session()->regenerate();
        $request->session()->flash('show_terms_modal', true);
        $this->recordAuthEvent($request, 'Login', 'User logged in successfully via IDP authorization code flow.', $user);

        $redirectResponse = redirect($this->redirectPathByRole($user->user_role));
        return $this->attachIdpCookies($redirectResponse, $accessToken, $refreshToken);
    }

    public function logout(Request $request)
    {
        if (Auth::check()) {
            $this->recordAuthEvent($request, 'Logout', 'User logged out from the system.');
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        $logoutUrl = trim((string) config('services.idp.logout_url', ''));
        $response = $logoutUrl !== '' ? redirect()->away($logoutUrl) : redirect('/login');

        return $this->clearIdpCookies($response);
    }

    public function showLoginForm(Request $request)
    {
        if (Auth::check()) {
            return redirect($this->redirectPathByRole((string) optional(Auth::user())->user_role));
        }

        if ($this->useIdpAuth()) {
            if ($request->boolean('idp_error')) {
                return view('login');
            }

            $authorizeUrl = $this->buildAuthorizeUrl();
            if ($authorizeUrl === null) {
                return view('login')->withErrors([
                    'idp' => 'Identity provider login is enabled but not configured.',
                ]);
            }

            return redirect()->away($authorizeUrl);
        }

        return view('login');
    }
}
