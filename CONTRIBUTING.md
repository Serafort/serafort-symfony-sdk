# Contributing

## Setup

```bash
composer install
```

## Git hooks

This repo ships a portable, dependency-free pre-commit hook (`.githooks/pre-commit`) that
runs `composer validate --strict` and `vendor/bin/phpunit` before every commit. Enable it
once per clone with:

```bash
git config core.hooksPath .githooks
```

## Tests

```bash
composer test
# or
vendor/bin/phpunit
```

## CI

GitHub Actions (`.github/workflows/ci.yml`) validates `composer.json` and runs the PHPUnit
suite on PHP 8.2 and 8.3 for every push and pull request against `main`.
