<?php

declare(strict_types=1);

namespace Laminas\ComposerAutoloading\Exception;

use JsonException;

use function sprintf;

final class ComposerJsonFileException extends RuntimeException
{
    public static function forFileNotFound(string $filename): self
    {
        return new self(sprintf(
            'File "%s" does not exist',
            $filename
        ));
    }

    public static function forUnreadableFile(string $composerJsonFile): self
    {
        return new self(sprintf(
            '"%s" file is unreadable',
            $composerJsonFile
        ));
    }

    public static function forUnparseableFile(
        string $composerJsonFile,
        string $composerJson,
        JsonException $previousException
    ): self {
        return new self(sprintf(
            "Unable to parse '%s': %s\nJSON:\n%s",
            $composerJsonFile,
            $composerJson,
            $previousException->getMessage()
        ), (int) $previousException->getCode(), $previousException);
    }

    public static function forUnserializableContents(JsonException $previousException): self
    {
        return new self(sprintf(
            'Unable to serialize composer.json contents to JSON: %s',
            $previousException->getMessage()
        ), (int) $previousException->getCode(), $previousException);
    }

    public static function forUnwriteableFile(string $composerJsonFile): self
    {
        return new self(sprintf(
            '"%s" file is read-only',
            $composerJsonFile
        ));
    }
}
