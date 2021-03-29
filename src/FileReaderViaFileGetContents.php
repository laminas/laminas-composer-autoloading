<?php

/**
 * @see       https://github.com/laminas/laminas-composer-autoloading for the canonical source repository
 * @copyright https://github.com/laminas/laminas-composer-autoloading/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-composer-autoloading/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Laminas\ComposerAutoloading;

use function file_exists;
use function file_get_contents;
use function is_readable;

final class FileReaderViaFileGetContents implements FileReaderInterface
{
    public function __invoke(string $filename): string
    {
        if (! file_exists($filename)) {
            throw Exception\ComposerJsonFileException::forFileNotFound($filename);
        }

        if (! is_readable($filename)) {
            throw Exception\ComposerJsonFileException::forUnreadableFile($filename);
        }

        return file_get_contents($filename);
    }
}
