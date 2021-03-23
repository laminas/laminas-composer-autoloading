laminas-composer-autoloading
=======================

[![Build Status](https://github.com/laminas/laminas-composer-autoloading/workflows/Continuous%20Integration/badge.svg)](https://github.com/laminas/laminas-composer-autoloading/actions?query=workflow%3A"Continuous+Integration")

Introduction
------------

The `laminas-composer-autoloading` package provides the following commands for use with [laminas-cli](https://docs.laminas.dev/laminas-cli/):

- `composer:autoload:enable` - add the named module to the project autoloading rules defined in `composer.json`
- `composer:autoload:disable` - remove autoloading rules for the module from `composer.json`

Both commands also dump the autoloading rules on completion.

> ### Upgrading
>
> If you were using the v2 series of this component, the package previously provided its own binary, `laminas-composer-autoloading`.
> You will now call `laminas composer:autoload:(disable|enable)` instead.

Installation
------------

Run the following `composer` command:

```console
$ composer require --dev "laminas/laminas-composer-autoloading"
```

Note the `--dev` flag; this tool is intended for use in development only.

Usage
-----

```bash
# Enable the module "Foo" and autodetermine if PSR-0 or PSR-4 autoloading should be generated
$ ./vendor/bin/laminas composer:autoload:enable Foo
# Enable the module "Bar" using PSR-0 rules
$ ./vendor/bin/laminas composer:autoload:enable Bar --type psr-0
# Disable the module "Baz"
$ ./vendor/bin/laminas composer:autoload:disable Baz
```

Use `laminas help <command>` to get detailed help about available options and arguments.

### Notes

- Modules are assumed to have a `src/` directory. If they do not, the autoloading generated will be incorrect.
- If unable to determine the autoloading type, the command raises an exception.
- On enabling autoloading, if the `Module` class file for the module is in the module root, it will be moved to the module's `src/` directory (laminas-mvc applications only).

Examples
--------

1. Autodetect a module's autoloading type, and generate a Composer autoloading
   entry for "Status" module.

   ```bash
   $ ./vendor/bin/laminas composer:autoload:enable Status
   ```

1. Autodetect a module's autoloading type, and remove a Composer autoloading
   entry for "Status" module.

   ```bash
   $ ./vendor/bin/laminas composer:autoload:disable Status
   ```

1. Specify PSR-0 for the module type, and generate a Composer autoloading
   entry for "Status" module.

   ```bash
   $ ./vendor/bin/laminas composer:autoload:enable --type psr-0 Status
   ```

1. Specify PSR-4 for the module type, and generate a Composer autoloading
   entry for "Status" module.

   ```bash
   $ ./vendor/bin/laminas composer:autoload:enable --type psr-4 Status
   ```

1. Specify the path to the composer binary when generating autoloading entry
   for "Status" module:

   ```bash
   $ ./vendor/bin/laminas composer:autoload:enable -c composer.phar Status
   ```

1. Specify the path to modules directory, and generate a Composer autoloading
   entry for "Status" module.

   ```bash
   $ ./vendor/bin/laminas composer:autoload:enable -m src Status
   ```
