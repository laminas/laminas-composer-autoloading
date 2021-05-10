<?php

declare(strict_types=1);

namespace Laminas\ComposerAutoloading;

interface MoveModuleClassFileInterface
{
    /**
     * @psalm-param callable(string $originalFile, string $newFile):void $reporter
     */
    public function __invoke(string $modulePath, callable $reporter): void;
}
