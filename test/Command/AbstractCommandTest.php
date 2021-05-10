<?php

namespace LaminasTest\ComposerAutoloading\Command;

use InvalidArgumentException;
use Laminas\ComposerAutoloading\AutoloadDumpInterface;
use Laminas\ComposerAutoloading\FileReaderInterface;
use Laminas\ComposerAutoloading\FileWriterInterface;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;
use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractCommandTest extends TestCase
{
    /**
     * @var AutoloadDumpInterface|MockObject
     * @psalm-var AutoloadDumpInterface&MockObject
     */
    protected $autoloadDumper;

    /** @var Command */
    protected $command;

    /** @var vfsStreamDirectory */
    protected $dir;

    /**
     * @var FileReaderInterface|MockObject
     * @psalm-var FileReaderInterface&MockObject
     */
    protected $fileReader;

    /**
     * @var FileWriterInterface|MockObject
     * @psalm-var FileWriterInterface&MockObject
     */
    protected $fileWriter;

    /**
     * @var InputInterface|MockObject
     * @psalm-var InputInterface&MockObject
     */
    protected $input;

    /** @var vfsStreamDirectory */
    protected $modulesDir;

    /**
     * @var OutputInterface|MockObject
     * @psalm-var OutputInterface&MockObject
     */
    protected $output;

    protected function setUp(): void
    {
        $this->dir = vfsStream::setup('root', null, [
            'config' => [
                'config.php' => '<?php return [];',
            ],
            'module' => [],
        ]);

        $this->modulesDir     = $this->dir->getChild('module');
        $this->input          = $this->createMock(InputInterface::class);
        $this->output         = $this->createMock(OutputInterface::class);
        $this->fileReader     = $this->createMock(FileReaderInterface::class);
        $this->fileWriter     = $this->createMock(FileWriterInterface::class);
        $this->autoloadDumper = $this->createMock(AutoloadDumpInterface::class);
    }

    public function prepareReader(string $filename, string $jsonToReturn): void
    {
        $this->fileReader
            ->expects($this->once())
            ->method('__invoke')
            ->with($filename)
            ->willReturn($jsonToReturn);
    }

    /**
     * @return mixed
     */
    public function executeCommand(Command $command)
    {
        $r = new ReflectionMethod($command, 'execute');
        $r->setAccessible(true);
        return $r->invoke($command, $this->input, $this->output);
    }

    /**
     * @psalm-return iterable<string, array{
     *     0: string,
     *     1: string,
     *     2: string,
     *     3: string,
     *     4: non-empty-string,
     * }>
     */
    public function invalidCommandLines(): iterable
    {
        // phpcs:disable Generic.Files.LineLength.TooLong
        yield 'empty module name'          => ['', 'composer.phar', vfsStream::url('root/'), 'module', '<module>'];
        yield 'empty composer path'        => ['NewModule', '', vfsStream::url('root/'), 'module', '--composer'];
        yield 'non-directory project path' => ['NewModule', 'composer.phar', 'not-a-directory', 'module', '--project-path'];
        yield 'empty modules path'         => ['NewModule', 'composer.phar', vfsStream::url('root/'), '', '--modules-path'];
        // phpcs:enable
    }

    /**
     * @dataProvider invalidCommandLines
     * @psalm-param non-empty-string $expectedMessage
     */
    public function testRaisesAssertionExceptionWhenOptionsOrArgumentsAreInvalid(
        string $module,
        string $composerPath,
        string $projectPath,
        string $modulePath,
        string $expectedMessage
    ): void {
        $this->input->expects($this->once())->method('getArgument')->with('modulename')->willReturn($module);
        $this->input
            ->expects($this->exactly(3))
            ->method('getOption')
            ->withConsecutive(
                ['composer'],
                ['project-path'],
                ['modules-path']
            )
            ->willReturnOnConsecutiveCalls(
                $composerPath,
                $projectPath,
                $modulePath
            );

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage($expectedMessage);
        $this->executeCommand($this->command);
    }

    public function testRaisesAssertionWhenModuleDirectoryHasUnrecognizedStructure(): void
    {
        vfsStream::newDirectory('NewModule')->at($this->modulesDir);
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
                vfsStream::url('root/'),
                'module',
                null
            );

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Unable to determine autoloading type');
        $this->executeCommand($this->command);
    }

    public function testRaisesAssertionWhenUnableProvideWithInvalidType(): void
    {
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
                'not-a-valid-autoloader-type'
            );

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('--type');
        $this->executeCommand($this->command);
    }
}
