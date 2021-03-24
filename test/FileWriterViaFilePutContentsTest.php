<?php

/**
 * @see       https://github.com/laminas/laminas-composer-autoloading for the canonical source repository
 * @copyright https://github.com/laminas/laminas-composer-autoloading/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-composer-autoloading/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\ComposerAutoloading;

use Laminas\ComposerAutoloading\Exception\ComposerJsonFileException;
use Laminas\ComposerAutoloading\FileWriterViaFilePutContents;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use PHPUnit\Framework\TestCase;

use function file_get_contents;

class FileWriterViaFilePutContentsTest extends TestCase
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

    public function testRaisesExceptionIfDirectoryIsNotWritable(): void
    {
        vfsStream::newDirectory('test', 0111)->at($this->dir);
        $filename = vfsStream::url('root/test/newfile.txt');
        $writer   = new FileWriterViaFilePutContents();

        $this->expectException(ComposerJsonFileException::class);
        $this->expectExceptionMessage('read-only');
        $writer($filename, 'test contents');
    }

    public function testRaisesExceptionIfFileIsNotWritable(): void
    {
        vfsStream::newFile('test/newfile.txt', 0111)->at($this->dir);
        $filename = vfsStream::url('root/test/newfile.txt');
        $writer   = new FileWriterViaFilePutContents();

        $this->expectException(ComposerJsonFileException::class);
        $this->expectExceptionMessage('read-only');
        $writer($filename, 'test contents');
    }

    public function testWritesFileToFilesystem(): void
    {
        $contents = 'test contents';
        vfsStream::newFile('test/newfile.txt')->at($this->dir)->withContent('');
        $filename = vfsStream::url('root/test/newfile.txt');
        $writer   = new FileWriterViaFilePutContents();

        $this->assertNull($writer($filename, $contents));
        $this->assertSame($contents, file_get_contents($filename));
    }
}
