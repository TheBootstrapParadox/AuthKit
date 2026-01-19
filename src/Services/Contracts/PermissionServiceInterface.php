<?php

namespace BSPDX\AuthKit\Services\Contracts;

use BSPDX\AuthKit\Models\AuthKitPermission;
use Illuminate\Support\Collection;
use Illuminate\Contracts\Auth\Authenticatable;

interface PermissionServiceInterface
{
    /**
     * Get all permissions with their roles.
     *
     * @return Collection
     */
    public function getAllWithRoles(): Collection;

    /**
     * Create a new permission.
     *
     * @param string $name
     * @param string $guardName
     * @return AuthKitPermission
     */
    public function create(string $name, string $guardName = 'web'): AuthKitPermission;

    /**
     * Delete a permission.
     *
     * @param AuthKitPermission $permission
     * @return void
     */
    public function delete(AuthKitPermission $permission): void;

    /**
     * Sync permissions directly to a user.
     *
     * @param Authenticatable $user
     * @param array $permissions
     * @return void
     */
    public function syncToUser(Authenticatable $user, array $permissions): void;

    /**
     * Get direct permissions assigned to a user.
     *
     * @param Authenticatable $user
     * @return Collection
     */
    public function getUserPermissions(Authenticatable $user): Collection;

    /**
     * Get all permissions for a user (including via roles).
     *
     * @param Authenticatable $user
     * @return Collection
     */
    public function getAllUserPermissions(Authenticatable $user): Collection;
}
