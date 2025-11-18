<?php

namespace BSPDX\AuthKit;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use BSPDX\AuthKit\Http\Middleware\EnsureHasRole;
use BSPDX\AuthKit\Http\Middleware\EnsureHasPermission;
use BSPDX\AuthKit\Http\Middleware\EnsureTwoFactorEnabled;

class AuthKitServiceProvider extends ServiceProvider {
    /**
     * Register any application services.
     */
    public function register(): void {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/authkit.php',
            'authkit'
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void {
        // Load package routes if enabled
        if (config('authkit.load_routes', false)) {
            if (
                !file_exists(base_path('routes/authkit-web.php')) &&
                !file_exists(base_path('routes/authkit-api.php'))
            ) {
                $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
                $this->loadRoutesFrom(__DIR__ . '/../routes/api.php');
            }
        }

        // Load migrations
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        // Load views
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'authkit');

        // Publish configuration
        $this->publishes([
            __DIR__ . '/../config/authkit.php' => config_path('authkit.php'),
        ], 'authkit-config');

        // Publish migrations
        $this->publishes([
            __DIR__ . '/../database/migrations' => database_path('migrations'),
        ], 'authkit-migrations');

        // Publish views
        $this->publishes([
            __DIR__ . '/../resources/views' => resource_path('views/vendor/authkit'),
        ], 'authkit-views');

        // Publish seeders
        $this->publishes([
            __DIR__ . '/../database/seeders' => database_path('seeders'),
        ], 'authkit-seeders');

        // Publish example routes
        $this->publishes([
            __DIR__ . '/../routes/web.php' => base_path('routes/authkit-web.php'),
            __DIR__ . '/../routes/api.php' => base_path('routes/authkit-api.php'),
        ], 'authkit-routes');

        // Register middleware aliases
        $router = $this->app['router'];
        $router->aliasMiddleware('role', EnsureHasRole::class);
        $router->aliasMiddleware('permission', EnsureHasPermission::class);
        $router->aliasMiddleware('2fa', EnsureTwoFactorEnabled::class);

        // Register Blade components
        $this->loadViewComponentsAs('authkit', [
            \BSPDX\AuthKit\View\Components\LoginForm::class,
            \BSPDX\AuthKit\View\Components\RegisterForm::class,
            \BSPDX\AuthKit\View\Components\TwoFactorChallenge::class,
            \BSPDX\AuthKit\View\Components\PasskeyRegister::class,
            \BSPDX\AuthKit\View\Components\PasskeyLogin::class,
        ]);
    }
}
