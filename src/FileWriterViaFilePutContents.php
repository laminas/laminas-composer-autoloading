<?php

/**
 * @see       https://github.com/laminas/laminas-composer-autoloading for the canonical source repository
 * @copyright https://github.com/laminas/laminas-composer-autoloading/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-composer-autoloading/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Laminas\ComposerAutoloading;

use function file_put_contents;
use function is_writable;

final class FileWriterViaFilePutContents implements FileWriterInterface
{
    public function __invoke(string $filename, string $contents): void
    {
        if (! is_writable($filename)) {
            throw Exception\ComposerJsonFileException::forUnwriteableFile($filename);
        }

        file_put_contents($filename, $contents);
    }
}
