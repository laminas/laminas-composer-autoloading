<?php

declare(strict_types=1);

namespace Laminas\ComposerAutoloading;

interface FileWriterInterface
{
    public function __invoke(string $filename, string $contents): void;
}
