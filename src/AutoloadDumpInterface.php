<?php

declare(strict_types=1);

namespace Laminas\ComposerAutoloading;

interface AutoloadDumpInterface
{
    public function __invoke(string $composerPath): void;
}
