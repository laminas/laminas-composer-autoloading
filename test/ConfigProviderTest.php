<?php

/**
 * @see       https://github.com/laminas/laminas-composer-autoloading for the canonical source repository
 * @copyright https://github.com/laminas/laminas-composer-autoloading/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-composer-autoloading/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace LaminasTest\ComposerAutoloading;

use Laminas\ComposerAutoloading\Command\DisableCommand;
use Laminas\ComposerAutoloading\Command\EnableCommand;
use Laminas\ComposerAutoloading\ConfigProvider;
use PHPUnit\Framework\TestCase;

class ConfigProviderTest extends TestCase
{
    public function testProviderHasExpectedTopLevelKeys(): void
    {
        $provider = new ConfigProvider();
        $config   = $provider();

        $this->assertArrayHasKey('dependencies', $config);
        $this->assertArrayHasKey('laminas-cli', $config);
    }

    /** @psalm-return array<string, array{0: class-string, 1: non-empty-string}> */
    public function expectedCommandFactoryKeys(): array
    {
        return [
            'DisableCommand' => [DisableCommand::class, 'composer:autoload:disable'],
            'EnableCommand'  => [EnableCommand::class, 'composer:autoload:enable'],
        ];
    }

    /**
     * @dataProvider expectedCommandFactoryKeys
     * @psalm-param class-string $commandClass
     */
    public function testDependenciesIncludesFactoriesForEachCommand(string $commandClass): void
    {
        $provider     = new ConfigProvider();
        $dependencies = $provider->getDependencies();
        $this->assertArrayHasKey('factories', $dependencies);

        $factories = $dependencies['factories'];
        $this->assertArrayHasKey($commandClass, $factories);
    }

    /**
     * @dataProvider expectedCommandFactoryKeys
     * @psalm-param class-string $commandClass
     * @psalm-param non-empty-string $command
     */
    public function testConsoleConfigMapsCommandNamesToCommandClasses(string $commandClass, string $command): void
    {
        $provider = new ConfigProvider();
        $config   = $provider->getConsoleConfig();
        $this->assertArrayHasKey('commands', $config);

        $commands = $config['commands'];
        $this->assertArrayHasKey($command, $commands);
        $this->assertSame($commandClass, $commands[$command]);
    }
}
