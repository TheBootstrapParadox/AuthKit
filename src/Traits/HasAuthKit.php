<?php

namespace BSPDX\AuthKit\Traits;

use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Spatie\LaravelPasskeys\Models\Concerns\InteractsWithPasskeys;

trait HasAuthKit
{
    use HasApiTokens;
    use TwoFactorAuthenticatable;
    use HasRoles;
    use InteractsWithPasskeys;

    /**
     * Determine if the user has enabled two-factor authentication.
     */
    public function hasTwoFactorEnabled(): bool
    {
        return !is_null($this->two_factor_secret) &&
               !is_null($this->two_factor_confirmed_at);
    }

    /**
     * Determine if the user has registered any passkeys.
     */
    public function hasPasskeysRegistered(): bool
    {
        return $this->passkeys()->exists();
    }

    /**
     * Determine if 2FA is required for this user based on their roles.
     */
    public function requires2FA(): bool
    {
        $requiredRoles = config('authkit.two_factor.required_for_roles', []);

        if (empty($requiredRoles)) {
            return false;
        }

        return $this->hasAnyRole($requiredRoles);
    }

    /**
     * Determine if passkeys are required for this user based on their roles.
     */
    public function requiresPasskey(): bool
    {
        $requiredRoles = config('authkit.passkey.required_for_roles', []);

        if (empty($requiredRoles)) {
            return false;
        }

        return $this->hasAnyRole($requiredRoles);
    }

    /**
     * Get the user's authentication methods.
     */
    public function getAuthenticationMethods(): array
    {
        return [
            'password' => true,
            'two_factor' => $this->hasTwoFactorEnabled(),
            'passkey' => $this->hasPasskeysRegistered(),
        ];
    }

    /**
     * Determine if the user is a super admin.
     */
    public function isSuperAdmin(): bool
    {
        $superAdminRole = config('authkit.rbac.super_admin_role', 'super-admin');

        return $this->hasRole($superAdminRole);
    }

    /**
     * Check if user can bypass permission checks (super admin).
     */
    public function canBypassPermissions(): bool
    {
        return $this->isSuperAdmin();
    }
}
