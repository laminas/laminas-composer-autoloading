<?php

declare(strict_types=1);

namespace LaminasTest\ComposerAutoloading\Command;

use Laminas\ComposerAutoloading\Command\DisableCommand;
use org\bovigo\vfs\vfsStream;

class DisableCommandTest extends AbstractCommandTest
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->command = new DisableCommand($this->fileReader, $this->fileWriter, $this->autoloadDumper);
    }

    public function testReturnsEarlyWhenAutoloadingRulesDoNotExist(): void
    {
        $this->prepareReader(vfsStream::url('root/composer.json'), '{}');

        $this->fileWriter->expects($this->never())->method('__invoke');
        $this->autoloadDumper->expects($this->never())->method('__invoke');

        $this->input->expects($this->once())->method('getArgument')->with('modulename')->willReturn('OldModule');
        $this->input
            ->expects($this->exactly(4))
            ->method('getOption')
            ->withConsecutive(
                ['composer'],
                ['project-path'],
                ['modules-path'],
                ['type']
            )
            ->willReturnOnConsecutiveCalls(
                'composer.phar',
                vfsStream::url('root'),
                'module',
                'psr-4'
            );

        $this->output
            ->expects($this->once())
            ->method('writeln')
            ->with($this->stringContains('No psr-4 autoloading rules exist'));

        $this->assertSame(0, $this->executeCommand($this->command));
    }

    public function testRemovesAutoloaderEntryUpdatesPackageAndDumpsAutoloader(): void
    {
        vfsStream::newDirectory('OldModule')->at($this->modulesDir);

        $this->prepareReader(
            vfsStream::url('root/composer.json'),
            <<<'END'
                {
                    "autoload": {
                        "psr-4": {
                            "OldModule\\": "module/OldModule/src/"
                        }
                    }
                }
                END
        );

        $this->fileWriter
            ->expects($this->once())
            ->method('__invoke')
            ->with(
                vfsStream::url('root/composer.json'),
                <<<'END'
                    {
                        "autoload": {
                            "psr-4": []
                        }
                    }
                    
                    END
            );

        $this->autoloadDumper
            ->expects($this->once())
            ->method('__invoke')
            ->with('composer.phar');

        $this->input->expects($this->once())->method('getArgument')->with('modulename')->willReturn('OldModule');
        $this->input
            ->expects($this->exactly(4))
            ->method('getOption')
            ->withConsecutive(
                ['composer'],
                ['project-path'],
                ['modules-path'],
                ['type']
            )
            ->willReturnOnConsecutiveCalls(
                'composer.phar',
                vfsStream::url('root'),
                'module',
                'psr-4'
            );

        $this->output
            ->expects($this->exactly(2))
            ->method('writeln')
            ->withConsecutive(
                [$this->stringContains('Removing psr-4 autoloading rule for module OldModule')],
                [$this->stringContains('Updating composer.json and dumping autoloader rules')]
            );

        $this->assertSame(0, $this->executeCommand($this->command));
    }
}
