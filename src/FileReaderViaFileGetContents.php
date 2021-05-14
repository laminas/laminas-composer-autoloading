<?php

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
