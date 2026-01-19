<?php

namespace BSPDX\AuthKit\Models;

use Spatie\Permission\Models\Role;

class AuthKitRole extends Role
{
    /**
     * Determine if this role is the super admin role.
     *
     * @return bool
     */
    public function isSuperAdmin(): bool
    {
        return $this->name === config('authkit.rbac.super_admin_role', 'super-admin');
    }
}
