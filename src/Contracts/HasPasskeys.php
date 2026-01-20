<?php

namespace BSPDX\AuthKit\Contracts;

use Spatie\LaravelPasskeys\Models\Concerns\HasPasskeys as SpatieHasPasskeys;

/**
 * Interface for models that support passkey authentication.
 *
 * This interface extends Spatie's HasPasskeys to allow importing
 * from AuthKit directly, abstracting the underlying implementation.
 */
interface HasPasskeys extends SpatieHasPasskeys
{
    //
}
