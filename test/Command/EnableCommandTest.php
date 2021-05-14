<?php

declare(strict_types=1);

namespace LaminasTest\ComposerAutoloading\Command;

use Closure;
use Laminas\ComposerAutoloading\Command\EnableCommand;
use Laminas\ComposerAutoloading\MoveModuleClassFileInterface;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\MockObject\MockObject;

class EnableCommandTest extends AbstractCommandTest
{
    /**
     * @var MoveModuleClassFileInterface|MockObject
     * @psalm-var MoveModuleClassFileInterface&MockObject
     */
    private $moduleFileMover;

    protected function setUp(): void
    {
        parent::setUp();

        $this->moduleFileMover = $this->createMock(MoveModuleClassFileInterface::class);

        $this->command = new EnableCommand(
            $this->fileReader,
            $this->fileWriter,
            $this->autoloadDumper,
            $this->moduleFileMover,
        );
    }

    public function testReturnsEarlyWhenAutoloadingRulesAlreadyExist(): void
    {
        $this->prepareReader(
            vfsStream::url('root/composer.json'),
            <<<'END'
                {
                    "autoload": {
                        "psr-4": {
                            "NewModule\\": "module/NewModule/src/"
                        }
                    }
                }
                END
        );

        $this->moduleFileMover->expects($this->never())->method('__invoke');
        $this->fileWriter->expects($this->never())->method('__invoke');
        $this->autoloadDumper->expects($this->never())->method('__invoke');

        $this->input->expects($this->once())->method('getArgument')->with('modulename')->willReturn('NewModule');
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

        $this->output->expects($this->once())->method('writeln')->with($this->stringContains('already exist'));

        $this->assertSame(0, $this->executeCommand($this->command));
    }

    public function testMovesModuleFileAddsAutoloaderEntryUpdatesPackageAndDumpsAutoloader(): void
    {
        vfsStream::newDirectory('NewModule')->at($this->modulesDir);

        $this->prepareReader(vfsStream::url('root/composer.json'), '{}');

        $this->moduleFileMover
            ->expects($this->once())
            ->method('__invoke')
            ->with(
                vfsStream::url('root/module'),
                $this->isInstanceOf(Closure::class)
            );

        $this->fileWriter
            ->expects($this->once())
            ->method('__invoke')
            ->with(
                vfsStream::url('root/composer.json'),
                <<<'END'
                    {
                        "autoload": {
                            "psr-4": {
                                "NewModule\\": "module/NewModule/src/"
                            }
                        }
                    }
                    
                    END
            );

        $this->autoloadDumper
            ->expects($this->once())
            ->method('__invoke')
            ->with('composer.phar');

        $this->input->expects($this->once())->method('getArgument')->with('modulename')->willReturn('NewModule');
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
                [$this->stringContains('Adding psr-4 autoloading rule for module NewModule')],
                [$this->stringContains('Updating composer.json and dumping autoloader rules')]
            );

        $this->assertSame(0, $this->executeCommand($this->command));
    }
}
