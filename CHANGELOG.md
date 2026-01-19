# Changelog

All notable changes to `bspdx/authkit` will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

**Note:** This CHANGELOG was created starting with v0.3.0. Changes prior to this version were not formally documented.

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
