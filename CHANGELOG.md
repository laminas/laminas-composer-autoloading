# Changelog

All notable changes to this project will be documented in this file, in reverse chronological order by release.

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
