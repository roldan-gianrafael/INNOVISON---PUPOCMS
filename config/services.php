<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'openai' => [
        'api_key' => env('OPENAI_API_KEY'),
        'model' => env('OPENAI_MODEL', 'gpt-4o-mini'),
    ],

    'idp' => [
        'enabled' => filter_var(env('IDP_ENABLED', false), FILTER_VALIDATE_BOOL),
        'base_url' => rtrim((string) env('IDP_BASE_URL', ''), '/'),
        'client_id' => env('IDP_CLIENT_ID'),
        'client_secret' => env('IDP_CLIENT_SECRET'),
        'redirect_uri' => env('IDP_REDIRECT_URI'),
        'authorize_path' => env('IDP_AUTHORIZE_PATH', '/api/v1/auth/authorize'),
        'authorize_include_redirect_uri' => filter_var(env('IDP_AUTHORIZE_INCLUDE_REDIRECT_URI', false), FILTER_VALIDATE_BOOL),
        'authorize_response_type' => env('IDP_AUTHORIZE_RESPONSE_TYPE', 'code'),
        'authorize_scope' => env('IDP_AUTHORIZE_SCOPE', ''),
        'token_path' => env('IDP_TOKEN_PATH', '/api/v1/auth/token'),
        'token_include_redirect_uri' => filter_var(env('IDP_TOKEN_INCLUDE_REDIRECT_URI', false), FILTER_VALIDATE_BOOL),
        'token_grant_type' => env('IDP_TOKEN_GRANT_TYPE', ''),
        'profile_paths' => array_values(array_filter(array_map('trim', explode(',', (string) env('IDP_PROFILE_PATHS', '/me,/auth/me,/userinfo'))))),
        'validate_token_path' => env('IDP_VALIDATE_TOKEN_PATH', '/api/validate-token'),
        'role_prefix' => env('IDP_ROLE_PREFIX', 'OCMS:'),
        'access_cookie_name' => env('IDP_ACCESS_COOKIE_NAME', 'access_token'),
        'refresh_cookie_name' => env('IDP_REFRESH_COOKIE_NAME', 'refresh_token'),
        'access_cookie_minutes' => (int) env('IDP_ACCESS_COOKIE_MINUTES', 60),
        'refresh_cookie_minutes' => (int) env('IDP_REFRESH_COOKIE_MINUTES', 10080),
        'cookie_secure' => filter_var(env('IDP_COOKIE_SECURE', true), FILTER_VALIDATE_BOOL),
        'cookie_same_site' => env('IDP_COOKIE_SAME_SITE', 'Lax'),
        'logout_url' => env('IDP_LOGOUT_URL'),
    ],

];
