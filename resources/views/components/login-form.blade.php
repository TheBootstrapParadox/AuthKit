<div class="authkit-form-container">
    @include('authkit::components.authkit-styles')

    <form method="POST" action="{{ $action }}" class="authkit-form">
        @csrf

        @if ($errors->any())
            <div class="authkit-error" style="margin-bottom: 1rem;">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <div class="authkit-form-group">
            <label for="email" class="authkit-label">Email</label>
            <input
                id="email"
                type="email"
                name="email"
                value="{{ old('email') }}"
                required
                autofocus
                class="authkit-input"
            >
            @error('email')
                <span class="authkit-error">{{ $message }}</span>
            @enderror
        </div>

        <div class="authkit-form-group">
            <label for="password" class="authkit-label">Password</label>
            <input
                id="password"
                type="password"
                name="password"
                required
                class="authkit-input"
            >
            @error('password')
                <span class="authkit-error">{{ $message }}</span>
            @enderror
        </div>

        @if ($showRememberMe)
            <div class="authkit-form-group">
                <label class="authkit-checkbox">
                    <input type="checkbox" name="remember" id="remember">
                    <span>Remember me</span>
                </label>
            </div>
        @endif

        <button type="submit" class="authkit-button">
            Log in
        </button>

        @if ($showPasskeyOption && config('authkit.features.passkeys'))
            <div class="authkit-divider">or</div>
            <button type="button" class="authkit-button authkit-button-secondary" onclick="loginWithPasskey()">
                Log in with Passkey
            </button>
        @endif

        <div class="authkit-links">
            @if ($showForgotPassword)
                <a href="{{ route('password.request') }}" class="authkit-link">Forgot password?</a>
            @endif
            @if ($showRegisterLink && config('authkit.features.registration'))
                <a href="{{ route('register') }}" class="authkit-link">Create account</a>
            @endif
        </div>
    </form>
</div>

@if ($showPasskeyOption && config('authkit.features.passkeys'))
<script>
    async function loginWithPasskey() {
        // Redirect to passkey login page or trigger passkey auth
        window.location.href = '{{ route('passkeys.login') }}';
    }
</script>
@endif
