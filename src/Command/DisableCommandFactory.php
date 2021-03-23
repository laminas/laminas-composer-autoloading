<?php

/**
 * @see       https://github.com/laminas/laminas-composer-autoloading for the canonical source repository
 * @copyright https://github.com/laminas/laminas-composer-autoloading/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-composer-autoloading/blob/master/LICENSE.md New BSD License
 */

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
