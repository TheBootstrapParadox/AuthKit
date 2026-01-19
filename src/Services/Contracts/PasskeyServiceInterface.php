<?php

namespace BSPDX\AuthKit\Services\Contracts;

use Illuminate\Contracts\Auth\Authenticatable;

interface PasskeyServiceInterface
{
    /**
     * Generate passkey registration options for a user.
     *
     * @param Authenticatable $user
     * @return array
     */
    public function registerOptions(Authenticatable $user): array;

    /**
     * Register a new passkey for the user.
     *
     * @param Authenticatable $user
     * @param array $credential
     * @param string $name
     * @return void
     */
    public function register(Authenticatable $user, array $credential, string $name): void;

    /**
     * Generate passkey authentication options.
     *
     * @return array
     */
    public function authenticationOptions(): array;

    /**
     * Authenticate a user using a passkey credential.
     *
     * @param array $credential
     * @return Authenticatable
     */
    public function authenticate(array $credential): Authenticatable;
}
