<?php

/**
 * @see       https://github.com/laminas/laminas-composer-autoloading for the canonical source repository
 * @copyright https://github.com/laminas/laminas-composer-autoloading/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-composer-autoloading/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Laminas\ComposerAutoloading;

interface MoveModuleClassFileInterface
{
    /**
     * @psalm-param callable(string $originalFile, string $newFile):void $reporter
     */
    public function __invoke(string $modulePath, callable $reporter): void;
}
