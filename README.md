# BSPDX AuthKit

[![Latest Version on Packagist](https://img.shields.io/packagist/v/bspdx/authkit.svg?style=flat-square)](https://packagist.org/packages/bspdx/authkit)
[![Total Downloads](https://img.shields.io/packagist/dt/bspdx/authkit.svg?style=flat-square)](https://packagist.org/packages/bspdx/authkit)
[![License](https://img.shields.io/packagist/l/bspdx/authkit.svg?style=flat-square)](https://packagist.org/packages/bspdx/authkit)

A comprehensive, production-ready authentication package for Laravel 12+ featuring:

- 🔐 **Standard Authentication** - Powered by Laravel Fortify
- 👥 **Role-Based Access Control (RBAC)** - Using Spatie Laravel Permission
- 📱 **TOTP Two-Factor Authentication** - Google Authenticator, Authy, etc.
- 🔑 **Passkey Authentication** - Modern WebAuthn/FIDO2 login
- 🛡️ **Passkey as 2FA** - Use passkeys as a second factor
- 🎨 **Framework-Agnostic Blade Components** - Beautiful, customizable UI partials
- 🌐 **API Support** - Full Sanctum integration for API authentication
- 🏢 **Multi-Tenancy Ready** - Optional tenant scoping

## Table of Contents

- [Requirements](#requirements)
- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)
  - [User Model Setup](#user-model-setup)
  - [Blade Components](#blade-components)
  - [Routes](#routes)
  - [Middleware](#middleware)
  - [API Usage](#api-usage)
- [HTTPS Setup](#https-setup)
- [Multi-Tenancy](#multi-tenancy)
- [Testing](#testing)
- [Credits](#credits)
- [License](#license)

## Requirements

- PHP 8.2+
- Laravel 12.0+
- MySQL 5.7+ / PostgreSQL 9.6+ / SQLite 3.8.8+

## Installation

### Step 1: Install via Composer

```bash
composer require bspdx/authkit
```

### Step 2: Publish Configuration & Assets

```bash
# Publish configuration
php artisan vendor:publish --tag=authkit-config

# Publish migrations
php artisan vendor:publish --tag=authkit-migrations

# Publish Blade views (optional - only if you want to customize)
php artisan vendor:publish --tag=authkit-views

# Publish example routes
php artisan vendor:publish --tag=authkit-routes

# Publish database seeders
php artisan vendor:publish --tag=authkit-seeders
```

### Step 3: Run Migrations

```bash
php artisan migrate
```

This will create tables for:
- Two-factor authentication columns in `users` table
- Roles and permissions (Spatie)
- Passkeys (Spatie)
- Personal access tokens (Sanctum)

### Step 4: Seed Demo Data (Optional)

```bash
php artisan db:seed --class=AuthKitSeeder
```

This creates:
- 4 default roles: `super-admin`, `admin`, `editor`, `user`
- Common permissions for each role
- 4 demo users (all with password: `password`)
  - `superadmin@example.com` - Super Admin
  - `admin@example.com` - Admin
  - `editor@example.com` - Editor
  - `user@example.com` - Regular User

### Step 5: Configure Fortify

In your `config/fortify.php`, ensure these features are enabled:

```php
'features' => [
    Features::registration(),
    Features::resetPasswords(),
    Features::emailVerification(),
    Features::updateProfileInformation(),
    Features::updatePasswords(),
    Features::twoFactorAuthentication([
        'confirm' => true,
        'confirmPassword' => true,
    ]),
],
```

## Configuration

The package configuration is located at `config/authkit.php`. Key settings:

### Enable/Disable Features

```php
'features' => [
    'registration' => true,
    'email_verification' => true,
    'two_factor' => true,
    'passkeys' => true,
    'passkey_2fa' => true,
    'api_tokens' => true,
],
```

### RBAC Settings

```php
'rbac' => [
    'multi_tenant' => false,
    'default_role' => 'user',
    'super_admin_role' => 'super-admin',
],
```

### Passkey Settings

```php
'passkey' => [
    'rp_name' => env('APP_NAME', 'Laravel'),
    'rp_id' => env('PASSKEY_RP_ID', 'localhost'),
    'user_verification' => 'preferred',
    'allow_multiple' => true,
    'required_for_roles' => [
        // 'admin',
    ],
],
```

### Two-Factor Settings

```php
'two_factor' => [
    'qr_code_size' => 200,
    'recovery_codes_count' => 8,
    'required_for_roles' => [
        // 'admin',
    ],
],
```

## Usage

### User Model Setup

Add the `HasAuthKit` trait to your `User` model:

```php
<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use BSPDX\AuthKit\Traits\HasAuthKit;

class User extends Authenticatable
{
    use Notifiable, HasAuthKit;

    // ... rest of your model
}
```

This trait combines:
- `HasApiTokens` (Sanctum)
- `TwoFactorAuthenticatable` (Fortify)
- `HasRoles` (Spatie Permission)
- `HasPasskeys` (Spatie Passkeys)

### Blade Components

AuthKit provides framework-agnostic Blade components you can drop anywhere:

#### Login Form

```blade
<x-authkit::login-form
    :show-passkey-option="true"
    :show-remember-me="true"
    :show-register-link="true"
    :show-forgot-password="true"
/>
```

#### Register Form

```blade
<x-authkit::register-form
    :show-login-link="true"
    :required-fields="['name', 'email', 'password', 'password_confirmation']"
/>
```

#### Two-Factor Challenge

```blade
<x-authkit::two-factor-challenge
    :show-recovery-code-option="true"
/>
```

#### Passkey Registration

```blade
<x-authkit::passkey-register />
```

#### Passkey Login

```blade
<x-authkit::passkey-login />
```

### Routes

AuthKit doesn't auto-register routes. Add them manually from the published examples:

**Web Routes** (`routes/authkit-web.php`):
```php
// Include in your routes/web.php
require __DIR__.'/authkit-web.php';
```

**API Routes** (`routes/authkit-api.php`):
```php
// Include in your routes/api.php
require __DIR__.'/authkit-api.php';
```

### Middleware

AuthKit provides three middleware aliases:

#### Role Middleware

```php
Route::middleware(['auth', 'role:admin'])->group(function () {
    // Only users with 'admin' role can access
});

// Multiple roles (OR logic)
Route::middleware(['auth', 'role:admin,editor'])->group(function () {
    // Users with 'admin' OR 'editor' role can access
});
```

#### Permission Middleware

```php
Route::middleware(['auth', 'permission:edit-posts'])->group(function () {
    // Only users with 'edit-posts' permission
});

// Multiple permissions
Route::middleware(['auth', 'permission:edit-posts,publish-posts'])->group(function () {
    // Users with either permission can access
});
```

#### 2FA Enforcement Middleware

```php
Route::middleware(['auth', '2fa'])->group(function () {
    // Ensures users with required roles have 2FA enabled
});
```

### Checking Permissions in Code

```php
// Check role
if (auth()->user()->hasRole('admin')) {
    // User is an admin
}

// Check permission
if (auth()->user()->can('edit-posts')) {
    // User can edit posts
}

// Check multiple roles
if (auth()->user()->hasAnyRole(['admin', 'editor'])) {
    // User has at least one of these roles
}

// Super admin check
if (auth()->user()->isSuperAdmin()) {
    // User is super admin (bypasses all permission checks)
}
```

### API Usage

#### Authentication

Use Sanctum for API authentication:

```php
// Login endpoint (you need to create this)
Route::post('/login', function (Request $request) {
    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    if (!Auth::attempt($credentials)) {
        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    $user = $request->user();
    $token = $user->createToken('api-token')->plainTextToken;

    return response()->json([
        'token' => $token,
        'user' => $user,
    ]);
});
```

#### API Endpoints

All API routes are protected with `auth:sanctum` middleware. Example requests:

**Get All Roles:**
```bash
curl -X GET http://localhost/api/roles \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept: application/json"
```

**Assign Role to User:**
```bash
curl -X POST http://localhost/api/users/1/roles \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"roles": ["admin"]}'
```

**Enable 2FA:**
```bash
curl -X POST http://localhost/api/user/two-factor-authentication \
  -H "Authorization: Bearer YOUR_TOKEN"
```

## HTTPS Setup

**Passkeys require HTTPS!** See our detailed guide: [HTTPS Setup for Laravel Sail](docs/https-setup.md)

Quick summary:

1. Install `mkcert`:
   ```bash
   brew install mkcert  # macOS
   mkcert -install
   ```

2. Generate certificates:
   ```bash
   mkdir -p docker/ssl && cd docker/ssl
   mkcert localhost 127.0.0.1 ::1
   mv localhost+2.pem cert.pem
   mv localhost+2-key.pem key.pem
   ```

3. Update `.env`:
   ```env
   APP_URL=https://localhost
   SESSION_SECURE_COOKIE=true
   ```

4. Configure Nginx/Caddy to use the certificates

See the full guide for detailed instructions.

## Multi-Tenancy

AuthKit is multi-tenancy ready. To enable:

### Step 1: Enable in Configuration

```php
// config/authkit.php
'multi_tenancy' => [
    'enabled' => true,
    'tenant_column' => 'tenant_id',
    'auto_scope' => true,
],

'rbac' => [
    'multi_tenant' => true,
],
```

### Step 2: Add Tenant Column to Migrations

Uncomment the tenant column in the published migration:

```php
// database/migrations/2024_01_01_000001_add_authkit_fields_to_users_table.php
$table->unsignedBigInteger('tenant_id')->nullable()->after('id');
$table->index('tenant_id');
```

### Step 3: Run Migrations

```bash
php artisan migrate
```

### Step 4: Scope Queries

Use Spatie's multitenancy package or implement your own scoping logic.

## Testing

Run the package tests:

```bash
composer test
```

Or with PHPUnit directly:

```bash
./vendor/bin/phpunit
```

## Customization

### Custom Blade Views

Publish the views and modify as needed:

```bash
php artisan vendor:publish --tag=authkit-views
```

Views will be in `resources/views/vendor/authkit/`.

### Custom Styling

All Blade components use CSS custom properties for easy theming:

```css
:root {
    --authkit-primary: #4f46e5;
    --authkit-primary-hover: #4338ca;
    --authkit-danger: #dc2626;
    --authkit-text: #1f2937;
    --authkit-border: #d1d5db;
    --authkit-bg: #ffffff;
    --authkit-radius: 0.5rem;
}
```

## Security

If you discover any security issues, please email security@bspdx.com instead of using the issue tracker.

## Credits

- [BSPDX](https://github.com/TheBootstrapParadox)
- Built on top of:
  - [Laravel Fortify](https://github.com/laravel/fortify)
  - [Laravel Sanctum](https://github.com/laravel/sanctum)
  - [Spatie Laravel Permission](https://github.com/spatie/laravel-permission)
  - [Spatie Laravel Passkeys](https://github.com/spatie/laravel-passkeys)

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.

---

## Quick Start Example

Here's a complete example to get you started quickly:

### 1. Install Package

```bash
composer require bspdx/authkit
php artisan vendor:publish --tag=authkit-config
php artisan vendor:publish --tag=authkit-migrations
php artisan migrate
php artisan db:seed --class=AuthKitSeeder
```

### 2. Update User Model

```php
<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use BSPDX\AuthKit\Traits\HasAuthKit;

class User extends Authenticatable
{
    use HasAuthKit;

    protected $fillable = ['name', 'email', 'password'];
}
```

### 3. Create Login Page

```blade
<!-- resources/views/auth/login.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <x-authkit::login-form />
</body>
</html>
```

### 4. Add Routes

```php
// routes/web.php
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

// Include AuthKit routes
require __DIR__.'/authkit-web.php';
```

### 5. Test It Out

```bash
# Start server (with HTTPS for passkeys)
./vendor/bin/sail up

# Visit https://localhost/login
# Use demo credentials: admin@example.com / password
```

That's it! You now have a complete authentication system with 2FA, passkeys, and RBAC.

## Support

- **Documentation:** [Full documentation](https://github.com/TheBootstrapParadox/AuthKit/wiki)
- **Issues:** [GitHub Issues](https://github.com/TheBootstrapParadox/AuthKit/issues)
- **Discussions:** [GitHub Discussions](https://github.com/TheBootstrapParadox/AuthKit/discussions)
