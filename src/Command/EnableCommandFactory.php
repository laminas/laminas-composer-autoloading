<?php

declare(strict_types=1);

namespace Laminas\ComposerAutoloading\Command;

use Laminas\ComposerAutoloading\AutoloadDumpViaSystemProcess;
use Laminas\ComposerAutoloading\FileReaderViaFileGetContents;
use Laminas\ComposerAutoloading\FileWriterViaFilePutContents;
use Laminas\ComposerAutoloading\MoveModuleClassFileViaFileOperations;

final class EnableCommandFactory
{
    public function __invoke(): EnableCommand
    {
        return new EnableCommand(
            new FileReaderViaFileGetContents(),
            new FileWriterViaFilePutContents(),
            new AutoloadDumpViaSystemProcess(),
            new MoveModuleClassFileViaFileOperations()
        );
    }
}
