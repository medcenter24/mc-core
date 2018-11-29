# D H V24

[![Build Status](https://travis-ci.org/md-extension/d-h-v24.svg?branch=master)](https://travis-ci.org/md-extension/d-h-v24)
[![styleci](https://styleci.io/repos/CHANGEME/shield)](https://styleci.io/repos/CHANGEME)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/md-extension/d-h-v24/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/md-extension/d-h-v24/?branch=master)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/CHANGEME/mini.png)](https://insight.sensiolabs.com/projects/CHANGEME)
[![Coverage Status](https://coveralls.io/repos/github/md-extension/d-h-v24/badge.svg?branch=master)](https://coveralls.io/github/md-extension/d-h-v24?branch=master)

[![Packagist](https://img.shields.io/packagist/v/md-extension/d-h-v24.svg)](https://packagist.org/packages/md-extension/d-h-v24)
[![Packagist](https://poser.pugx.org/md-extension/d-h-v24/d/total.svg)](https://packagist.org/packages/md-extension/d-h-v24)
[![Packagist](https://img.shields.io/packagist/l/md-extension/d-h-v24.svg)](https://packagist.org/packages/md-extension/d-h-v24)

Package description: CHANGE ME

## Installation

Install via composer
```bash
composer require md-extension/d-h-v24
```

### Register Service Provider

**Note! This and next step are optional if you use laravel>=5.5 with package
auto discovery feature.**

Add service provider to `config/app.php` in `providers` section
```php
mdExtension\DHV24\ServiceProvider::class,
```

### Register Facade

Register package facade in `config/app.php` in `aliases` section
```php
mdExtension\DHV24\Facades\DHV24::class,
```

### Publish Configuration File

```bash
php artisan vendor:publish --provider="mdExtension\DHV24\ServiceProvider" --tag="config"
```

## Usage

CHANGE ME

## Security

If you discover any security related issues, please email 
instead of using the issue tracker.

## Credits

- [](https://github.com/md-extension/d-h-v24)
- [All contributors](https://github.com/md-extension/d-h-v24/graphs/contributors)

This package is bootstrapped with the help of
[melihovv/laravel-package-generator](https://github.com/melihovv/laravel-package-generator).
