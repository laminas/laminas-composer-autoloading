# laminas-composer-autoloading

[![Build Status](https://github.com/laminas/laminas-composer-autoloading/actions/workflows/continuous-integration.yml/badge.svg)](https://github.com/laminas/laminas-composer-autoloading/actions/workflows/continuous-integration.yml)
[![type-coverage](https://shepherd.dev/github/laminas/laminas-composer-autoloading/coverage.svg)](https://shepherd.dev/github/laminas/laminas-composer-autoloading)
[![Psalm level](https://shepherd.dev/github/laminas/laminas-composer-autoloading/level.svg)](https://shepherd.dev/github/laminas/laminas-composer-autoloading)

> ## 🇷🇺 Русским гражданам
>
> Мы, участники Laminas, родились и живем в разных странах. У многих из нас есть друзья, родственники и коллеги как в России, так и в Украине. Некоторые из нас родились в России. Некоторые из нас живут в России. У некоторых бабушки и дедушки сражались с фашистами во Второй мировой войне. Здесь никто не поддерживает фашизм.
>
> У одного из нас есть украинская родственница, которая спаслась из дома вместе с сыном. Поезд задержался из-за бомбежки на дороге впереди. У нас есть друзья, которые прячутся в бомбоубежищах. Мы с тревогой ждем весточки от них после воздушных налетов, которые беспорядочно наносят удары и попадают по больницам, школам, детским садам и домам. Мы не берем это из каких-либо СМИ. Мы наблюдаем это напрямую.
>
> Вы доверяете нам достаточно, чтоб использовать наши программы, и мы просим вас довериться нам вновь. Мы нуждаемся в помощи. Выходите и протестуйте против этой бесполезной войны. Остановите кровопролитие. Скажите "Нет войне!"
>
> ## 🇺🇸 To Citizens of Russia
>
> We at Laminas come from all over the world. Many of us have friends, family and colleagues in both Russia and Ukraine. Some of us were born in Russia. Some of us currently live in Russia. Some have grandparents who fought Nazis in World War II. Nobody here supports fascism.
>
> One team member has a Ukrainian relative who fled her home with her son. The train was delayed due to bombing on the road ahead. We have friends who are hiding in bomb shelters. We anxiously follow up on them after the air raids, which indiscriminately fire at hospitals, schools, kindergartens and houses. We're not taking this from any media. These are our actual experiences.
>
> You trust us enough to use our software. We ask that you trust us to say the truth on this. We need your help. Go out and protest this unnecessary war. Stop the bloodshed. Say "stop the war!"

> [!CAUTION]
>
> ## Maintenance mode
>
> This package is considered feature-complete, and is now in **security-only** maintenance mode, following a decision by the Technical Steering Committee.
> More information on this decision can be found in a blog post: [Laminas MVC End of Life Schedule](https://getlaminas.org/blog/2026-03-06-laminas-mvc-eol-schedule.html).  
> The security-only status will continue until the security support for PHP 8.4 ends, which will be 31st December 2028.
>
> If you have a security issue, please [follow our security reporting guidelines](https://getlaminas.org/security/).

## Introduction

The `laminas-composer-autoloading` package provides the following commands for use with [laminas-cli](https://docs.laminas.dev/laminas-cli/):

- `composer:autoload:enable` - add the named module to the project autoloading rules defined in `composer.json`
- `composer:autoload:disable` - remove autoloading rules for the module from `composer.json`

Both commands also dump the autoloading rules on completion.

> ### Upgrading
>
> If you were using the v2 series of this component, the package previously provided its own binary, `laminas-composer-autoloading`.
> You will now call `laminas composer:autoload:(disable|enable)` instead.

## Installation

Run the following `composer` command:

```console
$ composer require --dev "laminas/laminas-composer-autoloading"
```

Note the `--dev` flag; this tool is intended for use in development only.

## Usage

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

## Examples

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
