<?php

/**
 * @see       https://github.com/laminas/laminas-composer-autoloading for the canonical source repository
 * @copyright https://github.com/laminas/laminas-composer-autoloading/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-composer-autoloading/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace LaminasTest\ComposerAutoloading;

use Laminas\ComposerAutoloading\AutoloadDumpInterface;
use Laminas\ComposerAutoloading\Composer;
use Laminas\ComposerAutoloading\FileReaderInterface;
use Laminas\ComposerAutoloading\FileWriterInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;

use function array_key_exists;
use function sprintf;

class ComposerTest extends TestCase
{
    /**
     * @var AutoloadDumpInterface|MockObject
     * @psalm-var AutoloadDumpInterface&MockObject
     */
    private $autoloadDumper;

    /**
     * @var FileReaderInterface|MockObject
     * @psalm-var FileReaderInterface&MockObject
     */
    private $fileReader;

    /**
     * @var FileWriterInterface|MockObject
     * @psalm-var FileWriterInterface&MockObject
     */
    private $fileWriter;

    public function setUp(): void
    {
        $this->fileReader     = $this->createMock(FileReaderInterface::class);
        $this->fileWriter     = $this->createMock(FileWriterInterface::class);
        $this->autoloadDumper = $this->createMock(AutoloadDumpInterface::class);
    }

    private function assertComposerUnchanged(Composer $composer): void
    {
        $r = new ReflectionProperty($composer, 'changed');
        $r->setAccessible(true);
        $this->assertFalse($r->getValue($composer), 'composer.json has changed, but was not expected to.');
    }

    private function assertAutoloadEntryExists(
        string $expectedKey,
        string $expectedPath,
        string $type,
        Composer $composer
    ): void {
        $r = new ReflectionProperty($composer, 'composer');
        $r->setAccessible(true);

        $definition = $r->getValue($composer);

        $this->assertArrayHasKey('autoload', $definition, sprintf(
            'Failed to determine if %s autoload definition for module %s exists; no autoload definitions exist',
            $type,
            $expectedKey
        ));

        $this->assertArrayHasKey($type, $definition['autoload'], sprintf(
            'Failed to determine if autoload definition for module %s exists; no %s autoload definitions exist',
            $expectedKey,
            $type
        ));

        $this->assertArrayHasKey($expectedKey, $definition['autoload'][$type], sprintf(
            '%s autoload definition for module %s does not exist',
            $type,
            $expectedKey
        ));

        $this->assertSame($expectedPath, $definition['autoload'][$type][$expectedKey], sprintf(
            '%s autoload definition for module %s does not match "%s"; found "%s"',
            $type,
            $expectedKey,
            $expectedPath,
            $definition['autoload'][$type][$expectedKey]
        ));
    }

    private function assertNotAutoloadEntryExists(
        string $moduleKey,
        string $type,
        Composer $composer
    ): void {
        $r = new ReflectionProperty($composer, 'composer');
        $r->setAccessible(true);

        $definition = $r->getValue($composer);

        if (! array_key_exists('autoload', $definition)) {
            return;
        }

        if (! array_key_exists($type, $definition['autoload'])) {
            return;
        }

        $this->assertArrayNotHasKey($moduleKey, $definition['autoload'][$type], sprintf(
            '%s autoload dfinition for module %s found, but should not have been',
            $type,
            $moduleKey
        ));
    }

    private function prepareReader(string $filename, string $jsonToReturn): void
    {
        $this->fileReader
            ->expects($this->once())
            ->method('__invoke')
            ->with($filename)
            ->willReturn($jsonToReturn);
    }

    public function testAddAutoloaderEntryDoesNothingIfRuleExistsForModule(): void
    {
        $this->prepareReader(
            './composer.json',
            <<<'END'
                {
                    "autoload": {
                        "psr-4": {
                            "ModuleName\\": "module/ModuleName/src/"
                        }
                    }
                }
                END
        );

        $composer = new Composer('.', $this->fileReader, $this->fileWriter, $this->autoloadDumper);

        $this->assertNull($composer->addAutoloaderEntry('psr-4', 'ModuleName', 'module'));
        $this->assertComposerUnchanged($composer);
    }

    /**
     * @psalm-return iterable<string, array{
     *     0: Composer::AUTOLOADER_PSR*,
     *     1: non-empty-string,
     *     2: non-empty-string,
     *     3: non-empty-string,
     *     4: non-empty-string,
     * }>
     */
    public function moduleProvider(): iterable
    {
        yield 'psr-4'        => ['psr-4', 'ModuleName', 'module', 'ModuleName\\', 'module/ModuleName/src/'];
        yield 'psr-0'        => ['psr-0', 'ModuleName', 'module', 'ModuleName\\', 'module/ModuleName/src/'];
        yield 'psr-4-custom' => ['psr-4', 'ModuleName', 'lib', 'ModuleName\\', 'lib/ModuleName/src/'];
        yield 'psr-0-custom' => ['psr-0', 'ModuleName', 'lib', 'ModuleName\\', 'lib/ModuleName/src/'];
    }

    /**
     * @dataProvider moduleProvider
     * @psalm-param Composer::AUTOLOADER_PSR* $type
     * @psalm-param non-empty-string $moduleName
     * @psalm-param non-empty-string $modulePath
     * @psalm-param non-empty-string $expectedKey
     * @psalm-param non-empty-string $expectedPath
     */
    public function testAddAutoloaderEntryAddsExpectedEntry(
        string $type,
        string $moduleName,
        string $modulePath,
        string $expectedKey,
        string $expectedPath
    ): void {
        $this->prepareReader('./composer.json', '{}');

        $composer = new Composer('.', $this->fileReader, $this->fileWriter, $this->autoloadDumper);

        $this->assertNull($composer->addAutoloaderEntry($type, $moduleName, $modulePath));
        $this->assertAutoloadEntryExists($expectedKey, $expectedPath, $type, $composer);
    }

    public function testRemoveAutoloaderEntryDoesNothingIfNoRuleExistsForModule(): void
    {
        $this->prepareReader('./composer.json', '{}');
        $composer = new Composer('.', $this->fileReader, $this->fileWriter, $this->autoloadDumper);

        $this->assertNull($composer->removeAutoloaderEntry('psr-4', 'ModuleName'));
        $this->assertComposerUnchanged($composer);
    }

    /**
     * @dataProvider moduleProvider
     * @psalm-param Composer::AUTOLOADER_PSR* $type
     * @psalm-param non-empty-string $moduleName
     * @psalm-param non-empty-string $pathToModules
     * @psalm-param non-empty-string $moduleKey
     * @psalm-param non-empty-string $modulePath
     */
    public function testRemoveAutoloaderEntryRemovesMatchedEntry(
        string $type,
        string $moduleName,
        string $pathToModules,
        string $moduleKey,
        string $modulePath
    ): void {
        $this->prepareReader(
            './composer.json',
            <<<END
                {
                    "autoload": {
                        "$type": {
                            "$moduleKey\\": "$modulePath"
                        }
                    }
                }
                END
        );

        $composer = new Composer('.', $this->fileReader, $this->fileWriter, $this->autoloadDumper);

        $this->assertNull($composer->removeAutoloaderEntry($type, $moduleName));
        $this->assertNotAutoloadEntryExists($moduleKey, $type, $composer);
    }

    public function testUpdatePackageAndDumpAutoloaderDoesNothingIfDefinitionIsUnchanged(): void
    {
        $this->fileWriter
            ->expects($this->never())
            ->method('__invoke');
        $this->autoloadDumper
            ->expects($this->never())
            ->method('__invoke');
        $this->prepareReader(
            './composer.json',
            <<<'END'
                {
                    "autoload": {
                        "psr-4": {
                            "ModuleName\\": "module/ModuleName/src/"
                        }
                    }
                }
                END
        );

        $composer = new Composer('.', $this->fileReader, $this->fileWriter, $this->autoloadDumper);

        $this->assertNull($composer->updatePackageAndDumpAutoloader('composer'));
    }

    public function testUpdatePackageAndDumpAutoloaderDoesWorkWhenDefinitionHasChanges(): void
    {
        $this->prepareReader(
            './composer.json',
            <<<'END'
                {
                    "autoload": {
                        "psr-4": {
                            "ModuleName\\": "module/ModuleName/src/"
                        }
                    }
                }
                END
        );

        $composer = new Composer('.', $this->fileReader, $this->fileWriter, $this->autoloadDumper);
        $composer->addAutoloaderEntry('psr-0', 'NewModule', 'module');

        $expectedJson = <<<'END'
            {
                "autoload": {
                    "psr-4": {
                        "ModuleName\\": "module/ModuleName/src/"
                    },
                    "psr-0": {
                        "NewModule\\": "module/NewModule/src/"
                    }
                }
            }

            END;

        $this->fileWriter
            ->expects($this->once())
            ->method('__invoke')
            ->with('./composer.json', $expectedJson);

        $this->autoloadDumper
            ->expects($this->once())
            ->method('__invoke')
            ->with('composer.phar');

        $this->assertNull($composer->updatePackageAndDumpAutoloader('composer.phar'));
    }
}
