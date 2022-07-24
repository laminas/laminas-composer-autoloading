<?php

declare(strict_types=1);

namespace LaminasTest\ComposerAutoloading;

use Laminas\ComposerAutoloading\MoveModuleClassFileViaFileOperations;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

use function file_get_contents;
use function sprintf;

class MoveModuleClassFileViaFileOperationsTest extends TestCase
{
    /** @var vfsStreamDirectory */
    private $dir;

    public function setUp(): void
    {
        $this->dir = vfsStream::setup('root', null, [
            'config' => [
                'config.php' => '<?php return [];',
            ],
            'module' => [
                'TestModule' => [
                    '.placeholder' => '',
                ],
            ],
        ]);
    }

    /**
     * @psalm-return callable(string $originalFile, string $newFile):void
     */
    private function createNoopReporter(): callable
    {
        return function (string $original, string $target): void {
            Assert::fail('Reporter was reached, but should not have been');
        };
    }

    /**
     * @psalm-return callable(string $originalFile, string $newFile):void
     */
    private function createSpyReporter(string $expectedOriginal, string $expectedTarget): callable
    {
        return function (string $original, string $target) use ($expectedOriginal, $expectedTarget): void {
            Assert::assertSame($expectedOriginal, $original, sprintf(
                'Did not receive expected original file "%s"; received "%s"',
                $expectedOriginal,
                $original
            ));

            Assert::assertSame($expectedTarget, $target, sprintf(
                'Did not receive expected target file "%s"; received "%s"',
                $expectedTarget,
                $target
            ));
        };
    }

    public function testReturnsEarlyIfModuleClassFileDoesNotExistInModuleRoot(): void
    {
        $mover = new MoveModuleClassFileViaFileOperations();
        $path  = vfsStream::url('root/module/TestModule');
        $this->assertNull($mover($path, $this->createNoopReporter()));
    }

    public function testReturnsEarlyIfModuleClassFileDoesNotContainModuleClass(): void
    {
        /** @var vfsStreamDirectory $modulePath */
        $modulePath = $this->dir->getChild('module/TestModule');
        vfsStream::newFile('Module.php')->at($modulePath)->setContent('<?php echo "Foo!";');

        $mover = new MoveModuleClassFileViaFileOperations();
        $path  = vfsStream::url('root/module/TestModule');
        $this->assertNull($mover($path, $this->createNoopReporter()));
    }

    public function testReturnsEarlyIfTargetModuleClassFileAlreadyExists(): void
    {
        /** @var vfsStreamDirectory $modulePath */
        $modulePath = $this->dir->getChild('module/TestModule');
        vfsStream::newFile('Module.php')->at($modulePath)->setContent(<<<'END'
            <?php
            
            namespace TestModule;

            class Module
            {
            }
            END);

        vfsStream::newDirectory('src')->at($modulePath);
        vfsStream::newFile('src/Module.php')->at($modulePath)->setContent('<?php echo "Foo!";');

        $mover = new MoveModuleClassFileViaFileOperations();
        $path  = vfsStream::url('root/module/TestModule');
        $this->assertNull($mover($path, $this->createNoopReporter()));
    }

    public function testMovesModuleClassFromModuleRootIntoModuleSourceDirectory(): void
    {
        /** @var vfsStreamDirectory $modulePath */
        $modulePath              = $this->dir->getChild('module/TestModule');
        $moduleClassFileContents = <<<'END'
            <?php
            
            namespace TestModule;

            class Module
            {
            }
            END;
        vfsStream::newFile('Module.php')->at($modulePath)->setContent($moduleClassFileContents);

        vfsStream::newDirectory('src')->at($modulePath);

        $mover = new MoveModuleClassFileViaFileOperations();
        $path  = vfsStream::url('root/module/TestModule');
        $this->assertNull($mover(
            $path,
            $this->createSpyReporter(
                vfsStream::url('root/module/TestModule/Module.php'),
                vfsStream::url('root/module/TestModule/src/Module.php')
            )
        ));

        $this->assertFileDoesNotExist(vfsStream::url('root/module/TestModule/Module.php'));
        $this->assertFileExists(vfsStream::url('root/module/TestModule/src/Module.php'));
        $this->assertSame(
            $moduleClassFileContents,
            file_get_contents(vfsStream::url('root/module/TestModule/src/Module.php'))
        );
    }

    public function testMovesModuleClassFromModuleRootIntoModuleSourceDirectoryAndRewritesDirReferences(): void
    {
        /** @var vfsStreamDirectory $modulePath */
        $modulePath              = $this->dir->getChild('module/TestModule');
        $moduleClassFileContents = <<<'END'
            <?php
            
            namespace TestModule;

            class Module
            {
                public function getConfig(): array
                {
                    return include __DIR__ . '/config/module.config.php';
                }
            }
            END;
        vfsStream::newFile('Module.php')->at($modulePath)->setContent($moduleClassFileContents);

        vfsStream::newDirectory('src')->at($modulePath);

        $expectedModuleClassFileContents = <<<'END'
            <?php
            
            namespace TestModule;

            class Module
            {
                public function getConfig(): array
                {
                    return include __DIR__ . '/../config/module.config.php';
                }
            }
            END;

        $mover = new MoveModuleClassFileViaFileOperations();
        $path  = vfsStream::url('root/module/TestModule');
        $this->assertNull($mover(
            $path,
            $this->createSpyReporter(
                vfsStream::url('root/module/TestModule/Module.php'),
                vfsStream::url('root/module/TestModule/src/Module.php')
            )
        ));

        $this->assertFileDoesNotExist(vfsStream::url('root/module/TestModule/Module.php'));
        $this->assertFileExists(vfsStream::url('root/module/TestModule/src/Module.php'));
        $this->assertSame(
            $expectedModuleClassFileContents,
            file_get_contents(vfsStream::url('root/module/TestModule/src/Module.php'))
        );
    }
}
