<?php

declare(strict_types=1);

namespace LaminasTest\ComposerAutoloading;

use Laminas\ComposerAutoloading\Exception\ComposerJsonFileException;
use Laminas\ComposerAutoloading\FileReaderViaFileGetContents;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use PHPUnit\Framework\TestCase;

class FileReaderViaFileGetContentsTest extends TestCase
{
    /** @var vfsStreamDirectory */
    private $dir;

    public function setUp(): void
    {
        $this->dir = vfsStream::setup('root', null, [
            'config' => [
                'config.php' => '<?php return [];',
            ],
            'module' => [],
        ]);
    }

    public function testRaisesExceptionIfFileDoesNotExist(): void
    {
        $filename = vfsStream::url('root/composer.json');
        $reader   = new FileReaderViaFileGetContents();

        $this->expectException(ComposerJsonFileException::class);
        $this->expectExceptionMessage('does not exist');
        $reader($filename);
    }

    public function testRaisesExceptionIfFileIsNotReadable(): void
    {
        vfsStream::newFile('composer.json', 0222)->at($this->dir);
        $filename = vfsStream::url('root/composer.json');
        $reader   = new FileReaderViaFileGetContents();

        $this->expectException(ComposerJsonFileException::class);
        $this->expectExceptionMessage('unreadable');
        $reader($filename);
    }

    public function testReturnsFileContentsIfFileExistsAndIsReadable(): void
    {
        $fileContent = '{"name": "laminas/laminas-composer-autoloading"}';
        vfsStream::newFile('composer.json')->at($this->dir)->withContent($fileContent);

        $filename = vfsStream::url('root/composer.json');
        $reader   = new FileReaderViaFileGetContents();

        $this->assertSame($fileContent, $reader($filename));
    }
}
