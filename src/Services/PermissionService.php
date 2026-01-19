<?php

namespace BSPDX\AuthKit\Services;

use BSPDX\AuthKit\Models\AuthKitPermission;
use BSPDX\AuthKit\Services\Contracts\PermissionServiceInterface;
use Illuminate\Support\Collection;
use Illuminate\Contracts\Auth\Authenticatable;

class PermissionService implements PermissionServiceInterface
{
    /**
     * Get all permissions with their roles.
     *
     * @return Collection
     */
    public function getAllWithRoles(): Collection
    {
        return AuthKitPermission::with('roles')->get();
    }

    /**
     * Create a new permission.
     *
     * @param string $name
     * @param string $guardName
     * @return AuthKitPermission
     */
    public function create(string $name, string $guardName = 'web'): AuthKitPermission
    {
        return AuthKitPermission::create([
            'name' => $name,
            'guard_name' => $guardName,
        ]);
    }

    /**
     * Delete a permission.
     *
     * @param AuthKitPermission $permission
     * @return void
     */
    public function delete(AuthKitPermission $permission): void
    {
        $permission->delete();
    }

    /**
     * Sync permissions directly to a user.
     *
     * @param Authenticatable $user
     * @param array $permissions
     * @return void
     */
    public function syncToUser(Authenticatable $user, array $permissions): void
    {
        $user->syncPermissions($permissions);
    }

    /**
     * Get direct permissions assigned to a user.
     *
     * @param Authenticatable $user
     * @return Collection
     */
    public function getUserPermissions(Authenticatable $user): Collection
    {
        return $user->permissions;
    }

    /**
     * Get all permissions for a user (including via roles).
     *
     * @param Authenticatable $user
     * @return Collection
     */
    public function getAllUserPermissions(Authenticatable $user): Collection
    {
        return $user->getAllPermissions();
    }
}
