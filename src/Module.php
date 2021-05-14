<?php

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
