<?php

namespace BSPDX\AuthKit\Services;

use BSPDX\AuthKit\Services\Contracts\PasskeyServiceInterface;
use Illuminate\Contracts\Auth\Authenticatable;
use Spatie\Passkeys\Facades\Passkey;

class PasskeyService implements PasskeyServiceInterface
{
    /**
     * Generate passkey registration options for a user.
     *
     * @param Authenticatable $user
     * @return array
     */
    public function registerOptions(Authenticatable $user): array
    {
        return Passkey::registerOptions($user);
    }

    /**
     * Register a new passkey for the user.
     *
     * @param Authenticatable $user
     * @param array $credential
     * @param string $name
     * @return void
     */
    public function register(Authenticatable $user, array $credential, string $name): void
    {
        Passkey::register($user, $credential, $name);
    }

    /**
     * Generate passkey authentication options.
     *
     * @return array
     */
    public function authenticationOptions(): array
    {
        return Passkey::authenticationOptions();
    }

    /**
     * Authenticate a user using a passkey credential.
     *
     * @param array $credential
     * @return Authenticatable
     */
    public function authenticate(array $credential): Authenticatable
    {
        return Passkey::authenticate($credential);
    }
}
