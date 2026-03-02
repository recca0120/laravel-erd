# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [0.5.0] - 2026-03-03

### Fixed
- Fix `setupFakeDatabase` per-connection override — each connection now correctly looks up its own override instead of all connections sharing the same one
- Simplify `restoreDatabase` — remove pointless `migrate:rollback` on `:memory:` SQLite
- Fix CI: copy spatie migration stubs when files don't exist (gitignored)
- Fix CI: use env-based DB credentials in tests

### Changed
- Add lefthook with pint pre-commit hook
- Drop PHP 7.4/8.0 from CI matrix (no longer installable due to security advisories)
- Simplify GitHub Actions workflow: remove flaky `cache-extensions`, update `codecov-action` to v5
- Update tests for compatibility with PHPUnit 12 and Laravel 12
- Add connection overrides documentation in config file

## [0.4.0] - 2025-03-02

### Added
- Laravel 12 support

## [0.3.0] - 2024-11-14

### Added
- Per-connection configuration support via `laravel-erd.connections`

## [0.2.0] - 2024-11-04

### Added
- Support for `awobaz/compoships`

### Fixed
- README fixes

## [0.1.2] - 2024-09-05

### Fixed
- Bug fixes

## [0.1.1] - 2024-08-25

### Fixed
- Bug fixes

## [0.1.0] - 2024-08-25

### Changed
- Major refactor and improvements

## [0.0.3] - 2023-02-23

### Fixed
- Bug fixes

## [0.0.2] - 2023-02-15

### Fixed
- Bug fixes

## [0.0.1] - 2023-01-18

### Added
- Initial release

[0.5.0]: https://github.com/recca0120/laravel-erd/compare/0.4.0...0.5.0
[0.4.0]: https://github.com/recca0120/laravel-erd/compare/0.3.0...0.4.0
[0.3.0]: https://github.com/recca0120/laravel-erd/compare/0.2.0...0.3.0
[0.2.0]: https://github.com/recca0120/laravel-erd/compare/0.1.2...0.2.0
[0.1.2]: https://github.com/recca0120/laravel-erd/compare/0.1.1...0.1.2
[0.1.1]: https://github.com/recca0120/laravel-erd/compare/0.1.0...0.1.1
[0.1.0]: https://github.com/recca0120/laravel-erd/compare/0.0.3...0.1.0
[0.0.3]: https://github.com/recca0120/laravel-erd/compare/0.0.2...0.0.3
[0.0.2]: https://github.com/recca0120/laravel-erd/compare/0.0.1...0.0.2
[0.0.1]: https://github.com/recca0120/laravel-erd/releases/tag/0.0.1
