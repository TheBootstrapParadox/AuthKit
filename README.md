# BSPDX AuthKit

[![Latest Version on Packagist](https://img.shields.io/packagist/v/bspdx/authkit.svg?style=flat-square)](https://packagist.org/packages/bspdx/authkit)
[![Total Downloads](https://img.shields.io/packagist/dt/bspdx/authkit.svg?style=flat-square)](https://packagist.org/packages/bspdx/authkit)
[![License](https://img.shields.io/packagist/l/bspdx/authkit.svg?style=flat-square)](https://packagist.org/packages/bspdx/authkit)

A comprehensive, production-ready authentication package for Laravel 12. AuthKin combines the power of Laravel Fortify, Sanctum, Spatie Laravel Permission, and Spatie Laravel Passkeys to provide a full-featured auth system with:

-   🔐 **Standard Authentication** - Powered by Laravel Fortify
-   👥 **Role-Based Access Control (RBAC)** - Using Spatie Laravel Permission
-   📱 **TOTP Two-Factor Authentication** - Google Authenticator, Authy, etc.
-   🔑 **Passkey Authentication** - Modern WebAuthn/FIDO2 login
-   🛡️ **Passkey as 2FA** - Use passkeys as a second factor
-   🎨 **Framework-Agnostic Blade Components** - Beautiful, customizable UI partials
-   🌐 **API Support** - Full Sanctum integration for API authentication
-   🏢 **Multi-Tenancy Ready** - Optional tenant scoping

## Table of Contents

-   [Requirements](#requirements)
-   [Installation](#installation)
-   [Configuration](#configuration)
-   [Usage](#usage)
    -   [User Model Setup](#user-model-setup)
    -   [Service Layer](#service-layer-new-in-v030)
    -   [Blade Components](#blade-components)
    -   [Routes](#routes)
    -   [Middleware](#middleware)
    -   [API Usage](#api-usage)
-   [Architecture](#architecture)
-   [HTTPS Setup](#https-setup)
-   [Multi-Tenancy](#multi-tenancy)
-   [Testing](#testing)
-   [Credits](#credits)
-   [License](#license)

## Requirements

-   PHP 8.2+
-   Laravel 12.0+
-   MySQL 5.7+ / PostgreSQL 9.6+ / SQLite 3.8.8+

## Installation

### Step 1: Install via Composer

```bash
composer require bspdx/authkit
```

### Step 2: Publish Configuration & Assets

```bash
# Publish the essentials: configuration and migrations
php artisan vendor:publish --tag=authkit-config --tag=authkit-migrations

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

-   Two-factor authentication columns in `users` table
-   Roles and permissions (Spatie)
-   Passkeys (Spatie)
-   Personal access tokens (Sanctum)

### Step 4: Seed Demo Data (Optional)

```bash
php artisan db:seed --class=AuthKitSeeder
```

This creates:

-   4 default roles: `super-admin`, `admin`, `editor`, `user`
-   Common permissions for each role
-   4 demo users (all with password: `password`)
    -   `superadmin@example.com` - Super Admin
    -   `admin@example.com` - Admin
    -   `editor@example.com` - Editor
    -   `user@example.com` - Regular User

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

-   `HasApiTokens` (Sanctum)
-   `TwoFactorAuthenticatable` (Fortify)
-   `HasRoles` (Spatie Permission)
-   `HasPasskeys` (Spatie Passkeys)

### Service Layer (NEW in v0.3.0)

AuthKit v0.3.0 introduces a clean service layer architecture to interact with roles, permissions, and passkeys. All external dependencies are now abstracted behind AuthKit services.

#### Using Services in Controllers

```php
<?php

namespace App\Http\Controllers;

use BSPDX\AuthKit\Services\Contracts\RoleServiceInterface;
use BSPDX\AuthKit\Services\Contracts\PermissionServiceInterface;
use BSPDX\AuthKit\Services\Contracts\AuthorizationServiceInterface;
use BSPDX\AuthKit\Services\Contracts\PasskeyServiceInterface;

class AdminController extends Controller
{
    public function __construct(
        private RoleServiceInterface $roleService,
        private PermissionServiceInterface $permissionService,
        private AuthorizationServiceInterface $authService
    ) {}

    public function assignRole(User $user)
    {
        // Get all roles
        $roles = $this->roleService->getAllWithPermissions();

        // Assign roles to user
        $this->authService->assignRolesToUser($user, ['admin', 'editor']);

        // Check if user has role
        if ($this->authService->userHasRole($user, 'admin')) {
            // User is admin
        }
    }
}
```

**Benefits:**
- Clean dependency injection
- Easy to mock for testing
- No direct external package dependencies in your code
- Future-proof architecture

### Blade Components (Optional)

AuthKit provides **optional** pre-built Blade components for Laravel projects. If you're using React, Vue, or another frontend framework, you can skip this section and use the JSON API endpoints instead.

**For Laravel Blade users:**

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

#### Traditional Approach (User Model Methods)

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

#### Service Layer Approach (Recommended for Controllers)

```php
use BSPDX\AuthKit\Services\Contracts\AuthorizationServiceInterface;

class PostController extends Controller
{
    public function __construct(
        private AuthorizationServiceInterface $authService
    ) {}

    public function edit(Post $post)
    {
        if ($this->authService->userHasPermission(auth()->user(), 'edit-posts')) {
            // User can edit posts
        }
    }
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

## Architecture

AuthKit v0.3.0+ uses a clean service layer architecture to isolate external dependencies.

### Service Layer

All role, permission, and passkey operations go through dedicated services:

- **PasskeyService** - Manages WebAuthn/passkey operations
  - `registerOptions()`, `register()`, `authenticationOptions()`, `authenticate()`
- **RoleService** - Role CRUD and queries
  - `getAllWithPermissions()`, `create()`, `delete()`, `syncPermissions()`
- **PermissionService** - Permission CRUD and queries
  - `getAllWithRoles()`, `create()`, `delete()`, `syncToUser()`
- **AuthorizationService** - High-level authorization operations
  - `assignRolesToUser()`, `assignPermissionsToUser()`, `userHasRole()`, `userHasPermission()`

All services are registered in Laravel's service container with interface bindings and convenient aliases:
- `authkit.passkey`
- `authkit.roles`
- `authkit.permissions`
- `authkit.authorization`

### Models

AuthKit provides its own model classes that extend Spatie's models:

- `BSPDX\AuthKit\Models\AuthKitRole` - Extends Spatie's Role model
  - Adds `isSuperAdmin()` method
- `BSPDX\AuthKit\Models\AuthKitPermission` - Extends Spatie's Permission model

All type hints use these AuthKit models, providing a consistent `BSPDX\AuthKit` namespace throughout your application.

### Benefits

- **Testability** - Mock service interfaces in tests instead of facades
- **Maintainability** - All external dependencies isolated in service layer
- **Flexibility** - Easy to swap implementations or add caching/logging
- **Clean API** - No third-party classes in your controllers

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

### Step 2: Scope Queries

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

If you discover any security issues, please email info@bspdx.com instead of using the issue tracker.

## Credits

-   [BSPDX](https://github.com/TheBootstrapParadox)
-   Built with:
    -   [Laravel Fortify](https://github.com/laravel/fortify)
    -   [Laravel Sanctum](https://github.com/laravel/sanctum)
    -   [Spatie Laravel Permission](https://github.com/spatie/laravel-permission) *(abstracted)*
    -   [Spatie Laravel Passkeys](https://github.com/spatie/laravel-passkeys) *(abstracted)*

**Note:** Starting with v0.3.0, all Spatie dependencies are abstracted through AuthKit's service layer, providing a clean `BSPDX\AuthKit` namespace throughout your application.

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

-   **Documentation:** [Full documentation](https://github.com/TheBootstrapParadox/AuthKit/wiki)
-   **Issues:** [GitHub Issues](https://github.com/TheBootstrapParadox/AuthKit/issues)
-   **Discussions:** [GitHub Discussions](https://github.com/TheBootstrapParadox/AuthKit/discussions)
