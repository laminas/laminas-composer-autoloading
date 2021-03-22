<?php

/**
 * @see       https://github.com/laminas/laminas-composer-autoloading for the canonical source repository
 * @copyright https://github.com/laminas/laminas-composer-autoloading/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-composer-autoloading/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\ComposerAutoloading;

use Laminas\ComposerAutoloading\Command;
use Laminas\ComposerAutoloading\Exception;
use Laminas\Stdlib\ConsoleHelper;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionObject;
use ReflectionProperty;

class CommandTest extends TestCase
{
    use MockeryPHPUnitIntegration;
    use ProjectSetupTrait;

    const TEST_COMMAND_NAME = 'laminas-composer-autoloading';

    /** @var vfsStreamDirectory */
    private $dir;

    /**
     * @var ConsoleHelper|MockObject
     * @psalm-var ConsoleHelper&MockObject
     */
    private $console;

    /** @var Command */
    private $command;

    protected function setUp(): void
    {
        parent::setUp();

        $this->dir = vfsStream::setup('project');

        $this->console = $this->createMock(ConsoleHelper::class);

        $this->command = new Command(self::TEST_COMMAND_NAME, $this->console);
        $this->setProjectDir($this->command, $this->dir->url());
    }

    /** @param mixed $expected */
    protected function assertAttributeSame(
        $expected,
        string $property,
        object $instance,
        string $message = ''
    ): void {
        $r = new ReflectionProperty($instance, $property);
        $r->setAccessible(true);
        self::assertSame($expected, $r->getValue($instance), $message);
    }

    /**
     * @psalm-return array<string, array<array-key, string[]>>
     */
    public function helpRequest(): array
    {
        return [
            'no-args'                     => [[]],
            'help-command'                => [['help']],
            'help-option'                 => [['--help']],
            'help-flag'                   => [['-h']],
            'enable-command-help-option'  => [['enable', '--help']],
            'enable-command-help-flag'    => [['enable', '-h']],
            'disable-command-help-option' => [['disable', '--help']],
            'disable-command-help-flag'   => [['disable', '-h']],
        ];
    }

    /**
     * @dataProvider helpRequest
     *
     * @param string[] $args
     *
     * @return void
     */
    public function testHelpRequestsEmitHelpToStdout(array $args): void
    {
        $this->assertHelpOutput();
        $this->assertEquals(0, $this->command->process($args));
    }

    /**
     * @psalm-return array<array-key, array{
     *     0: string,
     *     1: string,
     *     2: string,
     *     3: string,
     *     4: string,
     * }>
     */
    public function argument(): array
    {
        return [
            // $action, $argument,        $value,          $propertyName, $expectedValue
            ['enable',  '--composer',     'foo/bar',       'composer',    'foo/bar'],
            ['enable',  '-c',             'bar/baz',       'composer',    'bar/baz'],
            ['enable',  '--modules-path', './foo/modules', 'modulesPath', 'foo/modules'],
            ['enable',  '-p',             'bar\path',      'modulesPath', 'bar/path'],
            ['enable',  '--type',         'psr0',          'type',        'psr-0'],
            ['enable',  '--type',         'psr0',          'type',        'psr-0'],
            ['enable',  '-t',             'psr4',          'type',        'psr-4'],
            ['enable',  '-t',             'psr4',          'type',        'psr-4'],
            ['disable', '--composer',     'foo/bar',       'composer',    'foo/bar'],
            ['disable', '-c',             'bar/baz',       'composer',    'bar/baz'],
            ['disable', '--modules-path', 'foo/modules',   'modulesPath', 'foo/modules'],
            ['disable', '-p',             'bar/path',      'modulesPath', 'bar/path'],
            ['disable', '--type',         'psr0',          'type',        'psr-0'],
            ['disable', '--type',         'psr0',          'type',        'psr-0'],
            ['disable', '-t',             'psr4',          'type',        'psr-4'],
            ['disable', '-t',             'psr4',          'type',        'psr-4'],
        ];
    }

    /**
     * @dataProvider argument
     *
     * @return void
     */
    public function testArgumentIsSetAndHasExpectedValue(
        string $action,
        string $argument,
        string $value,
        string $propertyName,
        string $expectedValue
    ): void {
        $this->command->process([$action, $argument, $value, 'module-name']);

        $this->assertAttributeSame($expectedValue, $propertyName, $this->command);
    }

    public function testDefaultArgumentsValues(): void
    {
        $this->assertAttributeSame('module', 'modulesPath', $this->command);
        $this->assertAttributeSame('composer', 'composer', $this->command);
        $this->assertAttributeSame('', 'type', $this->command);
    }

    public function testUnknownCommandEmitsHelpToStderrWithErrorMessage(): void
    {
        $this->console
            ->expects($this->atLeastOnce())
            ->method('writeErrorMessage')
            ->with($this->stringContains('Unknown command'));
        $this->assertHelpOutput(STDERR);

        $this->assertEquals(1, $this->command->process(['foo', 'bar']));
    }

    /** @psalm-return array<string, array{0: string}> */
    public function action(): array
    {
        return [
            'disable' => ['disable'],
            'enable' => ['enable'],
        ];
    }

    /**
     * @dataProvider action
     *
     * @return void
     */
    public function testCommandErrorIfNoModuleNameProvided(string $action): void
    {
        $this->console
            ->expects($this->atLeastOnce())
            ->method('writeErrorMessage')
            ->with($this->stringContains('Invalid module name'));
        $this->assertHelpOutput(STDERR);

        $this->assertEquals(1, $this->command->process([$action]));
    }

    /**
     * @dataProvider action
     *
     * @return void
     */
    public function testCommandErrorIfInvalidNumberOfArgumentsProvided(string $action): void
    {
        $this->console
            ->expects($this->atLeastOnce())
            ->method('writeErrorMessage')
            ->with($this->stringContains('Invalid arguments'));
        $this->assertHelpOutput(STDERR);

        $this->assertEquals(1, $this->command->process([$action, 'invalid', 'module-name']));
    }

    /**
     * @dataProvider action
     *
     * @return void
     */
    public function testCommandErrorIfUnknownArgumentProvided(string $action): void
    {
        $this->console
            ->expects($this->atLeastOnce())
            ->method('writeErrorMessage')
            ->with($this->stringContains('Unknown argument "--invalid" provided'));
        $this->assertHelpOutput(STDERR);

        $this->assertEquals(1, $this->command->process([$action, '--invalid', 'value', 'module-name']));
    }

    /**
     * @runInSeparateProcess
     *
     * @preserveGlobalState disabled
     *
     * @dataProvider action
     *
     * @return void
     */
    public function testCommandErrorIfModulesDirectoryDoesNotExist(string $action): void
    {
        $this->console
            ->expects($this->atLeastOnce())
            ->method('writeErrorMessage')
            ->with($this->stringContains('Unable to determine modules directory'));
        $this->assertHelpOutput(STDERR);
        $this->assertComposerBinaryExecutable();

        $this->assertEquals(1, $this->command->process([$action, 'module-name']));
    }

    /**
     * @runInSeparateProcess
     *
     * @preserveGlobalState disabled
     *
     * @dataProvider action
     *
     * @return void
     */
    public function testCommandErrorIfModuleDoesNotExist(string $action): void
    {
        vfsStream::newDirectory('module')->at($this->dir);

        $this->console
            ->expects($this->atLeastOnce())
            ->method('writeErrorMessage')
            ->with($this->stringContains('Could not locate module "module-name"'));
        $this->assertHelpOutput(STDERR);
        $this->assertComposerBinaryExecutable();

        $this->assertEquals(1, $this->command->process([$action, 'module-name']));
    }

    /**
     * @runInSeparateProcess
     *
     * @preserveGlobalState disabled
     *
     * @dataProvider action
     *
     * @return void
     */
    public function testCommandErrorIfComposerIsNotExecutable(string $action): void
    {
        $modulesDir = vfsStream::newDirectory('module')->at($this->dir);
        $this->setUpModule($modulesDir, 'module-name', 'psr4');
        $this->setUpComposerJson($this->dir, []);

        $this->console
            ->expects($this->atLeastOnce())
            ->method('writeErrorMessage')
            ->with($this->stringContains('Unable to determine composer binary'));
        $this->assertHelpOutput(STDERR);
        $this->assertComposerBinaryNotExecutable();

        $this->assertEquals(1, $this->command->process([$action, 'module-name']));
    }

    /** @psalm-return array<string, array{0: string, 1: string}> */
    public function invalidType(): array
    {
        return [
            'enable-invalid-psr-0'  => ['enable', 'psr-0'],
            'enable-invalid-psr-4'  => ['enable', 'psr-4'],
            'disable-invalid-psr-0' => ['disable', 'psr-0'],
            'disable-invalid-psr-4' => ['disable', 'psr-4'],
        ];
    }

    /**
     * @dataProvider invalidType
     *
     * @return void
     */
    public function testCommandErrorIfInvalidTypeProvided(string $action, string $type): void
    {
        $modulesDir = vfsStream::newDirectory('module')->at($this->dir);
        $this->setUpModule($modulesDir, 'module-name', 'psr4');
        $this->setUpComposerJson($this->dir, []);

        $this->console
            ->expects($this->atLeastOnce())
            ->method('writeErrorMessage')
            ->with($this->stringContains('Invalid type provided; must be one of psr0 or psr4'));
        $this->assertHelpOutput(STDERR);

        $result = $this->command->process([$action, '--type', $type, 'module-name']);
        $this->assertEquals(1, $result);
    }

    /** @psalm-return array<string, array{0: string}> */
    public function type(): array
    {
        return [
            'psr-0' => ['psr0'],
            'psr-4' => ['psr4'],
        ];
    }

    /**
     * @runInSeparateProcess
     *
     * @dataProvider type
     *
     * @return void
     */
    public function testErrorMessageWhenActionProcessThrowsException(string $type): void
    {
        Mockery::mock('overload:' . MyTestingCommand::class)
            ->shouldReceive('process')
            ->with('App', $type === 'psr0' ? 'psr-0' : 'psr-4')
            ->andThrow(Exception\RuntimeException::class, 'Testing Exception Message')
            ->once();

        $modulesDir = vfsStream::newDirectory('module')->at($this->dir);
        $this->setUpModule($modulesDir, 'App', $type);

        $this->console
            ->expects($this->atLeastOnce())
            ->method('writeErrorMessage')
            ->with($this->stringContains('Testing Exception Message'));
        $this->assertNotHelpOutput(STDERR);
        $this->assertComposerBinaryExecutable();

        $this->injectCommand($this->command, 'my-command', MyTestingCommand::class);
        $this->assertEquals(1, $this->command->process(['my-command', '--type', $type, 'App']));
    }

    /**
     * @runInSeparateProcess
     *
     * @preserveGlobalState disabled
     *
     * @dataProvider type
     *
     * @return void
     */
    public function testMessageOnEnableWhenModuleIsAlreadyEnabled(string $type): void
    {
        Mockery::mock('overload:' . Command\Enable::class)
            ->shouldReceive('process')
            ->with('App', null)
            ->andReturn(false)
            ->once();

        $modulesDir = vfsStream::newDirectory('module')->at($this->dir);
        $this->setUpModule($modulesDir, 'App', $type);

        $this->console
            ->expects($this->once())
            ->method('writeLine')
            ->with('Autoloading rules already exist for the module "App"');
        $this->assertComposerBinaryExecutable();

        $this->assertEquals(0, $this->command->process(['enable', 'App']));
    }

    /**
     * @runInSeparateProcess
     *
     * @preserveGlobalState disabled
     *
     * @dataProvider type
     *
     * @return void
     */
    public function testSuccessMessageOnEnable(string $type): void
    {
        $mock = Mockery::mock('overload:' . Command\Enable::class);
        $mock
            ->shouldReceive('process')
            ->with('App', null)
            ->andReturn(true)
            ->once();
        $mock
            ->shouldReceive('getMovedModuleClass')
            ->withNoArgs()
            ->andReturnNull()
            ->once();

        $modulesDir = vfsStream::newDirectory('module')->at($this->dir);
        $this->setUpModule($modulesDir, 'App', $type);

        $this->console
             ->expects($this->exactly(2))
             ->method('writeLine')
             ->withConsecutive(
                 ['Successfully added composer autoloading for the module "App"'],
                 ['You can now safely remove the App\Module::getAutoloaderConfig() implementation.'],
             );
        $this->assertComposerBinaryExecutable();

        $this->assertEquals(0, $this->command->process(['enable', 'App']));
    }

    /**
     * @runInSeparateProcess
     *
     * @preserveGlobalState disabled
     *
     * @dataProvider type
     *
     * @return void
     */
    public function testSuccessMessageOnEnableAndModuleClassFileMoved(string $type): void
    {
        $mock = Mockery::mock('overload:' . Command\Enable::class);
        $mock
            ->shouldReceive('process')
            ->with('App', null)
            ->andReturn(true)
            ->once();
        $mock
            ->shouldReceive('getMovedModuleClass')
            ->withNoArgs()
            ->andReturn(['from-foo' => 'too-bar'])
            ->once();

        $modulesDir = vfsStream::newDirectory('module')->at($this->dir);
        $this->setUpModule($modulesDir, 'App', $type);

        $this->console
             ->expects($this->exactly(3))
             ->method('writeLine')
             ->withConsecutive(
                 ['Renaming from-foo to too-bar'],
                 ['Successfully added composer autoloading for the module "App"'],
                 ['You can now safely remove the App\Module::getAutoloaderConfig() implementation.'],
             );
        $this->assertComposerBinaryExecutable();

        $this->assertEquals(0, $this->command->process(['enable', 'App']));
    }

    /**
     * @runInSeparateProcess
     *
     * @preserveGlobalState disabled
     *
     * @dataProvider type
     *
     * @return void
     */
    public function testMessageOnDisableWhenModuleIsAlreadyDisabled(string $type): void
    {
        Mockery::mock('overload:' . Command\Disable::class)
            ->shouldReceive('process')
            ->with('App', null)
            ->andReturn(false)
            ->once();

        $modulesDir = vfsStream::newDirectory('module')->at($this->dir);
        $this->setUpModule($modulesDir, 'App', $type);

        $this->console
             ->expects($this->once())
             ->method('writeLine')
             ->with('Autoloading rules already do not exist for the module "App"');
        $this->assertComposerBinaryExecutable();

        $this->assertEquals(0, $this->command->process(['disable', 'App']));
    }

    /**
     * @runInSeparateProcess
     *
     * @preserveGlobalState disabled
     *
     * @dataProvider type
     *
     * @return void
     */
    public function testSuccessMessageOnDisable(string $type): void
    {
        Mockery::mock('overload:' . Command\Disable::class)
            ->shouldReceive('process')
            ->with('App', null)
            ->andReturn(true)
            ->once();

        $modulesDir = vfsStream::newDirectory('module')->at($this->dir);
        $this->setUpModule($modulesDir, 'App', $type);

        $this->console
             ->expects($this->atLeastOnce())
             ->method('writeLine')
             ->with('Successfully removed composer autoloading for the module "App"');
        $this->assertComposerBinaryExecutable();

        $this->assertEquals(0, $this->command->process(['disable', 'App']));
    }

    private function injectCommand(Command $command, string $cmd, string $class): void
    {
        $rCommand = new ReflectionObject($command);
        $rp = $rCommand->getProperty('commands');
        $rp->setAccessible(true);

        $commands = $rp->getValue($command);
        $commands[$cmd] = $class;

        $rp->setValue($command, $commands);
    }

    private function setProjectDir(Command $command, string $dir): void
    {
        $rc = new ReflectionObject($command);
        $rp = $rc->getProperty('projectDir');
        $rp->setAccessible(true);
        $rp->setValue($command, $dir);
    }

    /** @param resource $resource */
    private function assertHelpOutput($resource = STDOUT, string $command = self::TEST_COMMAND_NAME): void
    {
        $this->console
            ->expects($this->atLeastOnce())
            ->method('writeLine')
            ->with(
                $this->stringContains($command . ' [command] [options] modulename'),
                true,
                $resource
            );
    }

    /** @param resource $resource */
    private function assertNotHelpOutput($resource = STDOUT, string $command = self::TEST_COMMAND_NAME): void
    {
        $this->console
            ->expects($this->never())
            ->method('writeLine')
            ->with(
                $this->stringContains($command . ' [command] [options] modulename'),
                true,
                $resource
            );
    }

    private function assertComposerBinaryNotExecutable(): void
    {
        $exec   = $this->getFunctionMock('Laminas\ComposerAutoloading', 'exec');
        $exec->expects($this->once())->willReturnCallback(
            /**
             * @param null|string[] $output
             * @param null|int $retValue
             */
            function (string $command, &$output, &$retValue): void {
                $this->assertEquals('composer 2>&1', $command);
                $retValue = 1;
            }
        );
    }

    private function assertComposerBinaryExecutable(): void
    {
        $exec = $this->getFunctionMock('Laminas\ComposerAutoloading', 'exec');
        $exec->expects($this->once())->willReturnCallback(
            /**
             * @param null|string[] $output
             * @param null|int $retValue
             */
            function (string $command, &$output, &$retValue): void {
                $this->assertEquals('composer 2>&1', $command);
                $retValue = 0;
            }
        );
    }
}
