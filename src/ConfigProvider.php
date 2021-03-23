<?php

/**
 * @see       https://github.com/laminas/laminas-composer-autoloading for the canonical source repository
 * @copyright https://github.com/laminas/laminas-composer-autoloading/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-composer-autoloading/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Laminas\ComposerAutoloading;

final class ConfigProvider
{
    /** @psalm-return array<string, array<string, array<string, string>>> */
    public function __invoke(): array
    {
        return [
            'dependencies' => $this->getDependencies(),
            'laminas-cli'  => $this->getConsoleConfig(),
        ];
    }

    /** @psalm-return array<string, array<string, string>> */
    public function getConsoleConfig(): array
    {
        return [
            'commands' => [
                'composer:autoload:disable' => Command\DisableCommand::class,
                'composer:autoload:enable'  => Command\EnableCommand::class,
            ],
        ];
    }

    /** @psalm-return array<string, array<string, string>> */
    public function getDependencies(): array
    {
        return [
            'factories' => [
                Command\DisableCommand::class => Command\DisableCommandFactory::class,
                Command\EnableCommand::class  => Command\EnableCommandFactory::class,
            ],
        ];
    }
}
