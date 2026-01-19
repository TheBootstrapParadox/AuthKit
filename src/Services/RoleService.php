<?php

namespace BSPDX\AuthKit\Services;

use BSPDX\AuthKit\Models\AuthKitRole;
use BSPDX\AuthKit\Services\Contracts\RoleServiceInterface;
use Illuminate\Support\Collection;
use Illuminate\Contracts\Auth\Authenticatable;

class RoleService implements RoleServiceInterface
{
    /**
     * Get all roles with their permissions.
     *
     * @return Collection
     */
    public function getAllWithPermissions(): Collection
    {
        return AuthKitRole::with('permissions')->get();
    }

    /**
     * Create a new role.
     *
     * @param string $name
     * @param string $guardName
     * @return AuthKitRole
     */
    public function create(string $name, string $guardName = 'web'): AuthKitRole
    {
        return AuthKitRole::create([
            'name' => $name,
            'guard_name' => $guardName,
        ]);
    }

    /**
     * Delete a role.
     *
     * @param AuthKitRole $role
     * @return void
     * @throws \Exception if role cannot be deleted
     */
    public function delete(AuthKitRole $role): void
    {
        if ($role->isSuperAdmin()) {
            throw new \Exception('Cannot delete the super admin role.');
        }

        $role->delete();
    }

    /**
     * Sync permissions to a role.
     *
     * @param AuthKitRole $role
     * @param array $permissions
     * @return AuthKitRole
     */
    public function syncPermissions(AuthKitRole $role, array $permissions): AuthKitRole
    {
        $role->syncPermissions($permissions);

        return $role->load('permissions');
    }

    /**
     * Get all roles for a user.
     *
     * @param Authenticatable $user
     * @return Collection
     */
    public function getUserRoles(Authenticatable $user): Collection
    {
        return $user->roles;
    }
}
