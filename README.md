# AuthTemplate

A Laravel template package that includes auth with 2FA and passkeys.

## Features

This template is built on Laravel 12 and includes:

- **Laravel Framework ^12.0** - The latest Laravel framework
- **Inertia.js ^2.0** - Modern monolith with server-side routing and client-side rendering
- **Laravel Fortify ^1.31** - Backend authentication scaffolding including 2FA
- **Laravel Sanctum ^4.0** - API token authentication
- **Spatie Laravel Passkeys** - WebAuthn/Passkey authentication support
- **Ziggy ^2.0** - Use Laravel named routes in JavaScript

## Installation

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
npm install
npm run build
```

## Development

```bash
npm run dev
php artisan serve
```

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
