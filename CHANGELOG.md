# Changelog

All notable changes to `bspdx/authkit` will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

**Note:** This CHANGELOG was created starting with v0.3.0. Changes prior to this version were not formally documented.

---

## [0.5.3] 2026-01-21

#### Changed

- Hardened passkey flows: clone/transform WebAuthn options client-side, return the exact options JSON with credentials, and validate against stored options during registration/authentication (passkey Blade components, passkey controller/service contract and implementation).

---

## [0.5.2] 2026-01-21

#### Added

- Added `pragmarx/google2fa-laravel` to the package requirements so TOTP flows ship with the core install.

#### Changed

- Updated `resources/views/components/two-factor-challenge.blade.php` to use the shared `authkit-styles` partial for consistent theming and layout.
- AuthKit now registers the Fortify two-factor challenge view automatically when Fortify is present and no override is bound (see `AuthKitServiceProvider`).

---

## [0.5.1] 2026-01-21

#### Removed

- Removed `App\\` namespace from `autoload-dev` in composer.json (was used for local development only)

---

## [0.5.0] 2026-01-21

#### Added

**Passwordless Authentication**
- New `LoginController` to handle passwordless login methods and TOTP authentication
- New passwordless login routes and endpoints
- Support for multiple authentication methods per user

**User Profile Management**
- New `ProfileController` for user profile display and authentication preference updates
- New profile routes and endpoints
- New methods in `HasAuthKit` trait for managing authentication preferences and available methods

**Console Commands**
- New Artisan commands for role management
- New Artisan commands for permission management

**Component Samples**
- Added component samples for common authentication UI patterns

#### Changed

- Enhanced splash page with improved styling and content
- Updated `TwoFactorAuthController` to maintain backward compatibility for recovery codes

---

## [Unreleased] -  0.4.0

#### Changed

**Breaking Changes**
- **BREAKING:** `tenant_id` column is now always a UUID (was `unsignedBigInteger`)
- **BREAKING:** Permission table migrations now auto-detect User model ID type
  - If User model uses `HasUuids` trait, `model_morph_key` columns use `uuid`
  - Otherwise falls back to `unsignedBigInteger`
  - Detection uses `PasskeyConfig::getAuthenticatableModel()` to find the User model

#### Removed

- Removed `0001_01_01_00000_create_users_table.php` migration (conflicts with existing Laravel apps)
- Removed `0001_01_01_00001_create_cache_table.php` migration (host app responsibility)
- Removed `0001_01_01_00002_create_jobs_table.php` migration (host app responsibility)

#### Migration instructions

Review the [Migration Guide](MIGRATING-TO-SUTHKIT-0.4.0md) for help migrating to this new version. 

---

## [0.3.2] - 2026-01-20

#### Added

- New `BSPDX\AuthKit\Support\PasskeyConfig` class that wraps Spatie's passkey configuration
  - Provides `getAuthenticatableModel()`, `getPasskeyModel()`, `getRelyingPartyName()`, `getRelyingPartyId()`, `getRelyingPartyIcon()`, and `getRedirectAfterLogin()` methods
  - Migrations now use `PasskeyConfig` instead of Spatie's Config directly

---

## [0.3.1] - 2026-01-20

#### Added

- New `BSPDX\AuthKit\Contracts\HasPasskeys` interface that extends Spatie's `HasPasskeys`
  - Allows users to import the passkey interface from AuthKit instead of directly from Spatie
  - Provides abstraction layer for passkey authentication contracts

#### Usage

```php
use BSPDX\AuthKit\Contracts\HasPasskeys;

class User extends Authenticatable implements HasPasskeys
{
    use HasAuthKit;
    // ...
}
```

---

## [0.3.0] - 2026-01-19

#### Added

**Service Layer Architecture**
- New `PasskeyService` with interface for all passkey operations
- New `RoleService` with interface for role management
- New `PermissionService` with interface for permission management
- New `AuthorizationService` with interface for high-level authorization operations
- All services registered in Laravel container with interface bindings
- Service aliases: `authkit.passkey`, `authkit.roles`, `authkit.permissions`, `authkit.authorization`

**Model Proxies**
- New `BSPDX\AuthKit\Models\AuthKitRole` - Extends Spatie's Role model
- New `BSPDX\AuthKit\Models\AuthKitPermission` - Extends Spatie's Permission model
- New `AuthKitRole::isSuperAdmin()` method for checking super admin status

#### Changed

**Breaking Changes**
- **BREAKING:** Controllers now use dependency injection for services instead of direct Spatie imports
  - `PasskeyAuthController` now requires `PasskeyServiceInterface` injection
  - `RolePermissionController` now requires `RoleServiceInterface`, `PermissionServiceInterface`, and `AuthorizationServiceInterface` injection
- **BREAKING:** `config/permission.php` now references AuthKit models instead of Spatie models
  - `'models.role'` → `\BSPDX\AuthKit\Models\AuthKitRole::class`
  - `'models.permission'` → `\BSPDX\AuthKit\Models\AuthKitPermission::class`
- **BREAKING:** All public APIs now type-hint AuthKit models (`AuthKitRole`, `AuthKitPermission`) instead of Spatie models
- **BREAKING:** Route model binding for roles and permissions now uses AuthKit models

**Non-Breaking Changes**
- **Improved:** Complete isolation of Spatie dependencies behind service layer
- **Improved:** Controllers no longer contain direct `use Spatie\*` imports
- **Improved:** All external package usage confined to service implementations
- **Maintained:** `HasAuthKit` trait still uses Spatie trait composition (no performance impact)
- **Maintained:** All existing user-facing methods (`hasRole()`, `hasPermission()`, etc.) work unchanged

#### Usage for v0.3.0

Since AuthKit is in beta, refer to the updated README for current usage patterns.

**Using Services in Controllers:**
```php
use BSPDX\AuthKit\Services\Contracts\RoleServiceInterface;

class AdminController extends Controller
{
    public function __construct(
        private RoleServiceInterface $roleService
    ) {}

    public function index()
    {
        $roles = $this->roleService->getAllWithPermissions();
        // ...
    }
}
```

**Using AuthKit Models:**
```php
use BSPDX\AuthKit\Models\AuthKitRole;

$adminRole = AuthKitRole::where('name', 'admin')->first();
if ($adminRole->isSuperAdmin()) {
    // ...
}
```

**Configuration:**
The package's `config/permission.php` automatically uses AuthKit models. No manual changes needed unless you've published the config.

---

## [0.2.0] and earlier

Changes prior to v0.3.0 were not documented in this CHANGELOG.

For historical changes, please see the [Git commit history](https://github.com/TheBootstrapParadox/AuthKit/commits/main).
