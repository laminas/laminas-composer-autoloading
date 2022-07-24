# Changelog

All notable changes to this project will be documented in this file, in reverse chronological order by release.

## 3.2.0 - 2022-07-24


-----

### Release Notes for [3.2.0](https://github.com/laminas/laminas-composer-autoloading/milestone/10)

Feature release (minor)

### 3.2.0

- Total issues resolved: **0**
- Total pull requests resolved: **2**
- Total contributors: **2**

#### renovate

 - [14: Configure Renovate](https://github.com/laminas/laminas-composer-autoloading/pull/14) thanks to @renovate[bot]

#### Enhancement

 - [13: Prepare for Renovate with reusable workflows](https://github.com/laminas/laminas-composer-autoloading/pull/13) thanks to @ghostwriter

## 3.1.0 - 2021-10-21


-----

### Release Notes for [3.1.0](https://github.com/laminas/laminas-composer-autoloading/milestone/7)

Feature release (minor)

### 3.1.0

- Total issues resolved: **0**
- Total pull requests resolved: **2**
- Total contributors: **2**

#### Enhancement

 - [12: Add support for PHP 8.1](https://github.com/laminas/laminas-composer-autoloading/pull/12) thanks to @arueckauer
 - [10: Remove file headers](https://github.com/laminas/laminas-composer-autoloading/pull/10) thanks to @ghostwriter

## 2.3.0 - TBD

### Added

- Nothing.

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.

## 2.2.0 - 2021-03-22


-----

### Release Notes for [2.2.0](https://github.com/laminas/laminas-composer-autoloading/milestone/2)

### Added

- This release adds support for PHP 8.0.

### Removed

- This release removes support for PHP versions prior to 7.3.

### 2.2.0

- Total issues resolved: **1**
- Total pull requests resolved: **4**
- Total contributors: **2**

#### Enhancement

 - [8: Update to laminas-coding-standard v2](https://github.com/laminas/laminas-composer-autoloading/pull/8) thanks to @weierophinney
 - [7: Add Psalm integration](https://github.com/laminas/laminas-composer-autoloading/pull/7) thanks to @weierophinney
 - [6: Update to support PHP 8.0](https://github.com/laminas/laminas-composer-autoloading/pull/6) thanks to @weierophinney
 - [5: Switch from Travis to Laminas CI worfklow on GHA](https://github.com/laminas/laminas-composer-autoloading/pull/5) thanks to @weierophinney
 - [3: PHP 8.0 support](https://github.com/laminas/laminas-composer-autoloading/issues/3) thanks to @boesing

## 2.1.1 - 2020-04-20

### Added

- Nothing.

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [#1](https://github.com/laminas/laminas-composer-autoloading/pull/1) fixes an incorrect relative path when emitting help information, due to a malformed migration string.

## 2.1.0 - 2018-05-03

### Added

- [zfcampus/zf-composer-autoloading#10](https://github.com/zfcampus/zf-composer-autoloading/pull/10) adds support for PHP 7.2.

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- [zfcampus/zf-composer-autoloading#10](https://github.com/zfcampus/zf-composer-autoloading/pull/10) removes support for HHVM.

### Fixed

- Nothing.

## 2.0.0 - 2017-02-22

### Added

- [zfcampus/zf-composer-autoloading#8](https://github.com/zfcampus/zf-composer-autoloading/pull/8) extracts the classes
  `Laminas\ComposerAutoloading\Help`, `Laminas\ComposerAutoloading\Command\Enable`, and
  `Laminas\ComposerAutoloading\Command\Disable` from the `Laminas\ComposerAutoloading\Command` class,
  which now delegates to each of them to perform its tasks.

### Changes

- [zfcampus/zf-composer-autoloading#8](https://github.com/zfcampus/zf-composer-autoloading/pull/8) renames the
  script from `autoload-module-via-composer` to `laminas-composer-autoloading`.

- [zfcampus/zf-composer-autoloading#8](https://github.com/zfcampus/zf-composer-autoloading/pull/8) renames the
  `Command::__invoke()` method to `Command::process()`.

- [zfcampus/zf-composer-autoloading#8](https://github.com/zfcampus/zf-composer-autoloading/pull/8) adds a dependency
  on laminas-stdlib in order to facilitate colorized console reporting, and allow
  testing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.

## 1.1.1 - 2017-02-22

### Added

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [zfcampus/zf-composer-autoloading#5](https://github.com/zfcampus/zf-composer-autoloading/pull/5) fixes how
  the command creates the path to the module source directory; previously, it
  was hard-coded, and did not take into account the `-p`/`--modules-path`
  argument created in [zfcampus/zf-composer-autoloading#2](https://github.com/zfcampus/zf-composer-autoloading/pull/2).

- [zfcampus/zf-composer-autoloading#6](https://github.com/zfcampus/zf-composer-autoloading/pull/6) adds
  validation for the number of arguments, ensuring that no flags have empty
  values.

- [zfcampus/zf-composer-autoloading#7](https://github.com/zfcampus/zf-composer-autoloading/pull/7) adds
  validation of the composer binary in a cross-platform way; an exception is
  now raised if it is not executable.

## 1.1.0 - 2017-02-16

### Added

- [zfcampus/zf-composer-autoloading#2](https://github.com/zfcampus/zf-composer-autoloading/pull/2) adds the
  flags `-p`/`--modules-path`, allowing the user to specify the directory
  holding the module/source tree for which autoloading will be provided.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.

## 1.0.0 - 2016-08-12

Initial release.

### Added

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.
