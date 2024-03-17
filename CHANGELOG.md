# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/).

## [Unreleased]

[Unreleased]: https://github.com/php-parallel-lint/PHP-Parallel-Lint/compare/v1.3.2...HEAD


## [1.4.0] - 2024-03-27

### Added
- The "skip linting" feature can now be used in PHP files starting with a shebang, [#146] from [@xaben].

### Fixed
- PHP 8.4 deprecation notice, [#154] from [@Ayesh] and [@jrfnl].
- Bug fix: the PHP version check in the application bootstrap did not work on PHP < 5.3, [#100] from [@jrfnl], fixes [#62].
- Bug fix: files containing the `~` character in their name can now be processed correctly, [#118] from [@jrfnl].
- Bug fix: error message sometimes displayed on last line of code snippet, [#98] from [@jrfnl], fixes [#93].
- Bug fix: error message would sometimes contain duplicate information, [#117] from [@jrfnl].
- Bug fix: the "in file .. on line part" text did not always get cleaned correctly from the error message, [#118] from [@jrfnl].

### Changed
- The percentage output in the progress report is now aligned, [#140] from [@robertology].
- The error message displayed when the PHP version is too low for the application to run is now more informative, [#100] from [@jrfnl].
- Composer: The package will now identify itself as a static analysis/linting tool, [#134] from [@staabm].
- Composer: fix grammar error, [#139] from [@TravisCarden].
- README: improvement to the install instructions, [#99] from [@samsonasik], fixes [#96].
- README: move screenshot, [#97] from [@jrfnl].
- README: fix typos, [#124] from [@krsriq].
- Docs: code style consistency, [#137] from [@lens0021].

### Internal
- Prevent PHAR not being compatible with PHP < 7.0, [#116] from [@jrfnl].
- GH Actions: update used actions, [#109], [#158] from [@jrfnl].
- GH Actions: updates for box 4.x, [#121] from [@jrfnl].
- GH Actions: fix download URL for box, [#125] from [@jrfnl].
- GH Actions: use fail-fast with setup-php when creating the binaries, [#131], [#132] from [@jrfnl].
- GH Actions: update PHP version for PHAR boxing, [#152] from [@jrfnl].
- GH Actions: harden the workflow against PHPCS ruleset errors, [#128] from [@jrfnl].
- GH Actions: bust the cache semi-regularly, [#129] from [@jrfnl].
- GH Actions: update PHP versions in workflows, [#130] from [@jrfnl].
- GH Actions: update for the release of PHP 8.3, [#150], [#151] from [@jrfnl].
- GH Actions: fix duplicate release, [#159] from [@jrfnl].
- SettingsParseArgumentsTest: fix bug in test, [#102] from [@jrfnl].
- OutputTest: fix risky test, [#156] from [@jrfnl].
- Tests: fix issue with Nette Tester 1.x, [#141] from [@grogy].
- Add dependabot configuration file, [#148] from [@jrfnl].

[1.4.0]: https://github.com/php-parallel-lint/PHP-Parallel-Lint/compare/v1.3.2...v1.4.0

[#62]: https://github.com/php-parallel-lint/PHP-Parallel-Lint/issues/62
[#93]: https://github.com/php-parallel-lint/PHP-Parallel-Lint/issues/93
[#96]: https://github.com/php-parallel-lint/PHP-Parallel-Lint/issues/96
[#97]: https://github.com/php-parallel-lint/PHP-Parallel-Lint/pull/97
[#98]: https://github.com/php-parallel-lint/PHP-Parallel-Lint/pull/98
[#99]: https://github.com/php-parallel-lint/PHP-Parallel-Lint/pull/99
[#100]: https://github.com/php-parallel-lint/PHP-Parallel-Lint/pull/100
[#102]: https://github.com/php-parallel-lint/PHP-Parallel-Lint/pull/102
[#109]: https://github.com/php-parallel-lint/PHP-Parallel-Lint/pull/109
[#116]: https://github.com/php-parallel-lint/PHP-Parallel-Lint/pull/116
[#117]: https://github.com/php-parallel-lint/PHP-Parallel-Lint/pull/117
[#118]: https://github.com/php-parallel-lint/PHP-Parallel-Lint/pull/118
[#121]: https://github.com/php-parallel-lint/PHP-Parallel-Lint/pull/121
[#124]: https://github.com/php-parallel-lint/PHP-Parallel-Lint/pull/124
[#125]: https://github.com/php-parallel-lint/PHP-Parallel-Lint/pull/125
[#128]: https://github.com/php-parallel-lint/PHP-Parallel-Lint/pull/128
[#129]: https://github.com/php-parallel-lint/PHP-Parallel-Lint/pull/129
[#130]: https://github.com/php-parallel-lint/PHP-Parallel-Lint/pull/130
[#131]: https://github.com/php-parallel-lint/PHP-Parallel-Lint/pull/131
[#132]: https://github.com/php-parallel-lint/PHP-Parallel-Lint/pull/132
[#134]: https://github.com/php-parallel-lint/PHP-Parallel-Lint/pull/134
[#137]: https://github.com/php-parallel-lint/PHP-Parallel-Lint/pull/137
[#139]: https://github.com/php-parallel-lint/PHP-Parallel-Lint/pull/139
[#140]: https://github.com/php-parallel-lint/PHP-Parallel-Lint/pull/140
[#141]: https://github.com/php-parallel-lint/PHP-Parallel-Lint/pull/141
[#146]: https://github.com/php-parallel-lint/PHP-Parallel-Lint/pull/146
[#148]: https://github.com/php-parallel-lint/PHP-Parallel-Lint/pull/148
[#150]: https://github.com/php-parallel-lint/PHP-Parallel-Lint/pull/150
[#151]: https://github.com/php-parallel-lint/PHP-Parallel-Lint/pull/151
[#152]: https://github.com/php-parallel-lint/PHP-Parallel-Lint/pull/152
[#154]: https://github.com/php-parallel-lint/PHP-Parallel-Lint/pull/154
[#156]: https://github.com/php-parallel-lint/PHP-Parallel-Lint/pull/156
[#158]: https://github.com/php-parallel-lint/PHP-Parallel-Lint/pull/158
[#159]: https://github.com/php-parallel-lint/PHP-Parallel-Lint/pull/159


## [1.3.2] - 2022-02-19

### Added

- Support for PHP Console Highlighter 1.0.0, which comes with PHP Console Color 1.0.1, [#92] from [@jrfnl].

### Fixed

- Bug fix: make Phar file run independently of project under scan [#63] from [@jrfnl], fixes [#61].
- Bug fix: checkstyle report could contain invalid XML due to insufficient output escaping [#73] from [@gmazzap], fixes [#72].
- Fix Phar building [#70] from [@jrfnl]. This fixes PHP 8.1 compatibility for the Phar file.
- Documentation fix: the `--show-deprecated` option was missing in both the README as well as the CLI `help` [#84] from [@jrfnl], fixes [#81] reported by [@stronk7].

### Changed

- README: updated information about PHAR availability [#77] from [@jrfnl].
- README: updated CLI example [#80] from [@jrfnl].
- README: added documentation on how to exclude files from a scan based on the PHP version used [#80] from [@jrfnl].
- Composer autoload improvement [#88] from [@jrfnl] with thanks to [@mfn].

### Internal

- Welcome [@jrfnl] as a new maintainer [#32].
- GH Actions: set error reporting to E_ALL [#65], [#76] from [@jrfnl].
- GH Actions: fix failing tests on PHP 5.3-5.5 [#71] from [@jrfnl] and [@villfa].
- GH Actions: auto-cancel concurrent builds [#76] from [@jrfnl].
- GH Actions: testing against PHP 8.2 [#74] from [@grogy].
- GH Actions: release testing against PHP 5.3 [#79] from [@jrfnl].
- GH Actions: update used actions [#82] from [@jrfnl].
- Release checklist can now be found in the `.github` folder [#78] from [@jrfnl].

[1.3.2]: https://github.com/php-parallel-lint/PHP-Parallel-Lint/compare/v1.3.1...v1.3.2

[#32]: https://github.com/php-parallel-lint/PHP-Parallel-Lint/issues/32
[#61]: https://github.com/php-parallel-lint/PHP-Parallel-Lint/issues/61
[#63]: https://github.com/php-parallel-lint/PHP-Parallel-Lint/pull/63
[#65]: https://github.com/php-parallel-lint/PHP-Parallel-Lint/pull/65
[#70]: https://github.com/php-parallel-lint/PHP-Parallel-Lint/pull/70
[#71]: https://github.com/php-parallel-lint/PHP-Parallel-Lint/pull/71
[#72]: https://github.com/php-parallel-lint/PHP-Parallel-Lint/issues/72
[#73]: https://github.com/php-parallel-lint/PHP-Parallel-Lint/pull/73
[#74]: https://github.com/php-parallel-lint/PHP-Parallel-Lint/pull/74
[#76]: https://github.com/php-parallel-lint/PHP-Parallel-Lint/pull/76
[#77]: https://github.com/php-parallel-lint/PHP-Parallel-Lint/pull/77
[#78]: https://github.com/php-parallel-lint/PHP-Parallel-Lint/pull/78
[#79]: https://github.com/php-parallel-lint/PHP-Parallel-Lint/pull/79
[#80]: https://github.com/php-parallel-lint/PHP-Parallel-Lint/pull/80
[#81]: https://github.com/php-parallel-lint/PHP-Parallel-Lint/issues/81
[#82]: https://github.com/php-parallel-lint/PHP-Parallel-Lint/pull/82
[#84]: https://github.com/php-parallel-lint/PHP-Parallel-Lint/pull/84
[#88]: https://github.com/php-parallel-lint/PHP-Parallel-Lint/pull/88
[#89]: https://github.com/php-parallel-lint/PHP-Parallel-Lint/pull/89
[#92]: https://github.com/php-parallel-lint/PHP-Parallel-Lint/pull/92


## [1.3.1] - 2021-08-13

### Added

- Extend by the Code Climate output format [#50] from [@lukas9393]. 

### Fixed

- PHP 8.1: silence the deprecation notices about missing return types [#64] from [@jrfnl].

### Internal

- Reformat changelog to use reflinks in changelog entries [#58] from [@glensc].

[1.3.1]: https://github.com/php-parallel-lint/PHP-Parallel-Lint/compare/v1.3.0...v1.3.1

[#50]: https://github.com/php-parallel-lint/PHP-Parallel-Lint/pull/50
[#58]: https://github.com/php-parallel-lint/PHP-Parallel-Lint/pull/58
[#64]: https://github.com/php-parallel-lint/PHP-Parallel-Lint/pull/64

## [1.3.0] - 2021-04-07

### Added

- Allow for multi-part file extensions to be passed using -e (like `-e php,php.dist`) from [@jrfnl].
- Added syntax error callback [#30] from [@arxeiss].
- Ignore PHP startup errors [#34] from [@jrfnl].
- Restore php 5.3 support [#51] from [@glensc].

### Fixed

- Determine skip lint process failure by status code instead of stderr content [#48] from [@jankonas].

### Changed

- Improve wording in the readme [#52] from [@glensc].

### Internal

- Normalized composer.json from [@OndraM].
- Updated PHPCS dependency from [@jrfnl].
- Cleaned coding style from [@jrfnl].
- Provide one true way to run the test suite [#37] from [@mfn].
- Travis: add build against PHP 8.0 and fix failing test [#41] from [@jrfnl].
- GitHub Actions for testing, and automatic phar creation [#46] from [@roelofr].
- Add .github folder to .gitattributes export-ignore [#54] from [@reedy].
- Suggest to curl composer install via HTTPS [#53] from [@reedy].
- GH Actions: allow for manually triggering a workflow [#55] from [@jrfnl].
- GH Actions: fix phar creation [#55] from [@jrfnl].
- GH Actions: run the tests against all supported PHP versions [#55] from [@jrfnl].
- GH Actions: report CS violations in the PR [#55] from [@jrfnl].

[1.3.0]: https://github.com/php-parallel-lint/PHP-Parallel-Lint/compare/v1.2.0...v1.3.0
[#30]: https://github.com/php-parallel-lint/PHP-Parallel-Lint/pull/30
[#34]: https://github.com/php-parallel-lint/PHP-Parallel-Lint/pull/34
[#37]: https://github.com/php-parallel-lint/PHP-Parallel-Lint/pull/37
[#41]: https://github.com/php-parallel-lint/PHP-Parallel-Lint/pull/41
[#46]: https://github.com/php-parallel-lint/PHP-Parallel-Lint/pull/46
[#48]: https://github.com/php-parallel-lint/PHP-Parallel-Lint/pull/48
[#51]: https://github.com/php-parallel-lint/PHP-Parallel-Lint/pull/51
[#52]: https://github.com/php-parallel-lint/PHP-Parallel-Lint/pull/52
[#53]: https://github.com/php-parallel-lint/PHP-Parallel-Lint/pull/53
[#54]: https://github.com/php-parallel-lint/PHP-Parallel-Lint/pull/54
[#55]: https://github.com/php-parallel-lint/PHP-Parallel-Lint/pull/55

## [1.2.0] - 2020-04-04

### Added

- Added changelog.

### Fixed

- Fixed vendor location for running from other folder from [@Erkens].

### Internal

- Added a .gitattributes file from [@jrfnl], thanks for issue to [@ondrejmirtes].
- Fixed incorrect unit tests from [@jrfnl].
- Fixed minor grammatical errors from [@jrfnl].
- Added Travis: test against nightly (= PHP 8) from [@jrfnl].
- Travis: removed sudo from [@jrfnl].
- Added info about installing like not a dependency.
- Cleaned readme - new organization from previous package.
- Added checklist for new version from [@szepeviktor].

[1.2.0]: https://github.com/php-parallel-lint/PHP-Parallel-Lint/compare/v1.1.0...v1.2.0

[@arxeiss]:      https://github.com/arxeiss
[@Ayesh]:        https://github.com/Ayesh
[@Erkens]:       https://github.com/Erkens
[@glensc]:       https://github.com/glensc
[@gmazzap]:      https://github.com/gmazzap
[@grogy]:        https://github.com/grogy
[@jankonas]:     https://github.com/jankonas
[@jrfnl]:        https://github.com/jrfnl
[@krsriq]:       https://github.com/krsriq
[@lens0021]:     https://github.com/lens0021
[@lukas9393]:    https://github.com/lukas9393
[@mfn]:          https://github.com/mfn
[@OndraM]:       https://github.com/OndraM
[@ondrejmirtes]: https://github.com/ondrejmirtes
[@reedy]:        https://github.com/reedy
[@robertology]:  https://github.com/robertology
[@roelofr]:      https://github.com/roelofr
[@samsonasik]:   https://github.com/samsonasik
[@staabm]:       https://github.com/staabm
[@stronk7]:      https://github.com/stronk7
[@szepeviktor]:  https://github.com/szepeviktor
[@TravisCarden]: https://github.com/TravisCarden
[@villfa]:       https://github.com/villfa
[@xaben]:        https://github.com/xaben
