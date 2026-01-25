> # 🚨 Important update
> I just found out someone else made an AuthKit. You already know the one. I'll get around to renaming this soon, don't you worry!

# BSPDX AuthKit

[![Latest Version on Packagist](https://img.shields.io/packagist/v/bspdx/authkit.svg?style=flat-square)](https://packagist.org/packages/bspdx/authkit)
[![Total Downloads](https://img.shields.io/packagist/dt/bspdx/authkit.svg?style=flat-square)](https://packagist.org/packages/bspdx/authkit)
[![License](https://img.shields.io/packagist/l/bspdx/authkit.svg?style=flat-square)](https://packagist.org/packages/bspdx/authkit)

A comprehensive, production-ready authentication package for Laravel 12 with an **API-first architecture**. AuthKit combines the power of Laravel Fortify, Sanctum, Spatie Laravel Permission, and Spatie Laravel Passkeys to provide a full-featured auth system with:

-   🔐 **Standard Authentication** - Powered by Laravel Fortify
-   👥 **Role-Based Access Control (RBAC)** - Clean service layer API
-   📱 **TOTP Two-Factor Authentication** - Google Authenticator, Authy, etc.
-   🔑 **Passkey Authentication** - Modern WebAuthn/FIDO2 login
-   🛡️ **Passkey as 2FA** - Use passkeys as a second factor
-   🎨 **Optional Blade UI Components** - Pre-built views for Laravel projects
-   🌐 **API-First Design** - Works with React, Vue, mobile apps, or any frontend
-   🏢 **Multi-Tenancy Ready** - Optional tenant scoping

**bspdx/authkit** is now **bspdx/keystone**.

## Migration
```bash
composer remove bspdx/authkit
composer require bspdx/keystone
```
