<?php

namespace BSPDX\AuthKit\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class PasskeyLogin extends Component
{
    public function __construct(
        public ?string $loginOptionsUrl = null,
        public ?string $authenticateUrl = null,
    ) {
        $this->loginOptionsUrl = $loginOptionsUrl ?? route('passkeys.login.options');
        $this->authenticateUrl = $authenticateUrl ?? route('passkeys.authenticate');
    }

    public function render(): View
    {
        return view('authkit::components.passkey-login');
    }
}
