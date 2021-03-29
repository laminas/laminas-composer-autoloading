<?php

/**
 * @see       https://github.com/laminas/laminas-composer-autoloading for the canonical source repository
 * @copyright https://github.com/laminas/laminas-composer-autoloading/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-composer-autoloading/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Laminas\ComposerAutoloading;

final class Module
{
    /** @psalm-return array<string, array<string, array<string, string>>> */
    public function getConfig(): array
    {
        $configProvider = new ConfigProvider();
        return [
            'laminas-cli'     => $configProvider->getConsoleConfig(),
            'service_manager' => $configProvider->getDependencies(),
        ];
    }
}
