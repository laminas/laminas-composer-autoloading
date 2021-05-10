<?php

declare(strict_types=1);

namespace Laminas\ComposerAutoloading;

interface FileReaderInterface
{
    public function __invoke(string $filename): string;
}
