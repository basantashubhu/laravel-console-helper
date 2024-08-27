# Laravel Console Helper

The repository "laravel-console-helper" is a utility package for Laravel developers that provides a set of commands to make day-to-day Laravel development easier.

> **Note:** This package is for  development purposes only and is not intended for production use.

Any contributions are welcome. Please read the [contribution guidelines](CONTRIBUTING.md) before contributing.

## Installation

To install the package, run the following command:

```bash
composer require laravel-console-helper
```

### Common Utility Commands in Laravel Console Helper

```bash
php artisan make:class {name?}
```

Generates a new class file anywhere in the project even in the package directory.

```bash
php artisan make:composer-respository
```

Generates a new composer repository for custom packages to be added into composer.json