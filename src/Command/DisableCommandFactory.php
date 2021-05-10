<?php

declare(strict_types=1);

namespace Laminas\ComposerAutoloading\Command;

use Laminas\ComposerAutoloading\AutoloadDumpViaSystemProcess;
use Laminas\ComposerAutoloading\FileReaderViaFileGetContents;
use Laminas\ComposerAutoloading\FileWriterViaFilePutContents;

final class DisableCommandFactory
{
    public function __invoke(): DisableCommand
    {
        return new DisableCommand(
            new FileReaderViaFileGetContents(),
            new FileWriterViaFilePutContents(),
            new AutoloadDumpViaSystemProcess()
        );
    }
}
