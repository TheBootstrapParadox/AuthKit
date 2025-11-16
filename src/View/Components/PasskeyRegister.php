<?php

namespace BSPDX\AuthKit\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class PasskeyRegister extends Component
{
    public function __construct(
        public ?string $registerOptionsUrl = null,
        public ?string $registerUrl = null,
    ) {
        $this->registerOptionsUrl = $registerOptionsUrl ?? route('passkeys.register.options');
        $this->registerUrl = $registerUrl ?? route('passkeys.register');
    }

    public function render(): View
    {
        return view('authkit::components.passkey-register');
    }
}
