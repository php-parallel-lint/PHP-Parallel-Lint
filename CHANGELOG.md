# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/).

## [Unreleased]

[Unreleased]: https://github.com/php-parallel-lint/PHP-Parallel-Lint/compare/v1.3.0...HEAD

## [1.3.0] - 2021-04-07

### Added

- Allow for multi-part file extensions to be passed using -e (like `-e php,php.dist`) from [@jrfnl](https://github.com/jrfnl).
- Added syntax error callback [#30](https://github.com/php-parallel-lint/PHP-Parallel-Lint/pull/30) from [@arxeiss](https://github.com/arxeiss).
- Ignore PHP startup errors [#34](https://github.com/php-parallel-lint/PHP-Parallel-Lint/pull/34) from [@jrfnl](https://github.com/jrfnl).
- Restore php 5.3 support [#51](https://github.com/php-parallel-lint/PHP-Parallel-Lint/pull/51) from [@glensc](https://github.com/glensc).

### Fixed

- Determine skip lint process failure by status code instead of stderr content [#48](https://github.com/php-parallel-lint/PHP-Parallel-Lint/pull/48) from [@jankonas](https://github.com/jankonas).

### Changed

- Improve wording in the readme [#52](https://github.com/php-parallel-lint/PHP-Parallel-Lint/pull/52) from [@glensc](https://github.com/glensc).

### Internal

- Normalized composer.json from [@OndraM](https://github.com/OndraM).
- Updated PHPCS dependency from [@jrfnl](https://github.com/jrfnl).
- Cleaned coding style from [@jrfnl](https://github.com/jrfnl).
- Provide one true way to run the test suite [#37](https://github.com/php-parallel-lint/PHP-Parallel-Lint/pull/37) from [@mfn](https://github.com/mfn).
- Travis: add build against PHP 8.0 and fix failing test [#41](https://github.com/php-parallel-lint/PHP-Parallel-Lint/pull/41) from [@jrfnl](https://github.com/jrfnl).
- GitHub Actions for testing, and automatic phar creation [#46](https://github.com/php-parallel-lint/PHP-Parallel-Lint/pull/46) from [@roelofr](https://github.com/roelofr).
- Add .github folder to .gitattributes export-ignore [#54](https://github.com/php-parallel-lint/PHP-Parallel-Lint/pull/54) from [@glensc](https://github.com/glensc).
- Suggest to curl composer install via HTTPS [#53](https://github.com/php-parallel-lint/PHP-Parallel-Lint/pull/53) from [@reedy](https://github.com/reedy).
- GH Actions: allow for manually triggering a workflow [#55](https://github.com/php-parallel-lint/PHP-Parallel-Lint/pull/55) from [@jrfnl](https://github.com/jrfnl).
- GH Actions: fix phar creation [#55](https://github.com/php-parallel-lint/PHP-Parallel-Lint/pull/55) from [@jrfnl](https://github.com/jrfnl).
- GH Actions: run the tests against all supported PHP versions [#55](https://github.com/php-parallel-lint/PHP-Parallel-Lint/pull/55) from [@jrfnl](https://github.com/jrfnl).
- GH Actions: report CS violations in the PR [#55](https://github.com/php-parallel-lint/PHP-Parallel-Lint/pull/55) from [@jrfnl](https://github.com/jrfnl).

[1.3.0]: https://github.com/php-parallel-lint/PHP-Parallel-Lint/compare/v1.2.0...v1.3.0

## [1.2.0] - 2020-04-04

### Added

- Added changelog.

### Fixed

- Fixed vendor location for running from other folder from [@Erkens](https://github.com/Erkens).

### Internal

- Added a .gitattributes file from [@jrfnl](https://github.com/jrfnl), thanks for issue to [@ondrejmirtes](https://github.com/ondrejmirtes).
- Fixed incorrect unit tests from [@jrfnl](https://github.com/jrfnl).
- Fixed minor grammatical errors from [@jrfnl](https://github.com/jrfnl).
- Added Travis: test against nightly (= PHP 8) from [@jrfnl](https://github.com/jrfnl).
- Travis: removed sudo from [@jrfnl](https://github.com/jrfnl).
- Added info about installing like not a dependency.
- Cleaned readme - new organization from previous package.
- Added checklist for new version from [@szepeviktor](https://github.com/szepeviktor).

[1.2.0]: https://github.com/php-parallel-lint/PHP-Parallel-Lint/compare/v1.1.0...v1.2.0
