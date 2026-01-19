<?php

namespace BSPDX\AuthKit\Services\Contracts;

use BSPDX\AuthKit\Models\AuthKitRole;
use Illuminate\Support\Collection;
use Illuminate\Contracts\Auth\Authenticatable;

interface RoleServiceInterface
{
    /**
     * Get all roles with their permissions.
     *
     * @return Collection
     */
    public function getAllWithPermissions(): Collection;

    /**
     * Create a new role.
     *
     * @param string $name
     * @param string $guardName
     * @return AuthKitRole
     */
    public function create(string $name, string $guardName = 'web'): AuthKitRole;

    /**
     * Delete a role.
     *
     * @param AuthKitRole $role
     * @return void
     * @throws \Exception if role cannot be deleted
     */
    public function delete(AuthKitRole $role): void;

    /**
     * Sync permissions to a role.
     *
     * @param AuthKitRole $role
     * @param array $permissions
     * @return AuthKitRole
     */
    public function syncPermissions(AuthKitRole $role, array $permissions): AuthKitRole;

    /**
     * Get all roles for a user.
     *
     * @param Authenticatable $user
     * @return Collection
     */
    public function getUserRoles(Authenticatable $user): Collection;
}
