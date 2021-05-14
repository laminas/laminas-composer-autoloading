<?php

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
