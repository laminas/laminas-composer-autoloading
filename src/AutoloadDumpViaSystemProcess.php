<?php

/**
 * @see       https://github.com/laminas/laminas-composer-autoloading for the canonical source repository
 * @copyright https://github.com/laminas/laminas-composer-autoloading/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-composer-autoloading/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Laminas\ComposerAutoloading;

use function sprintf;
use function system;

final class AutoloadDumpViaSystemProcess implements AutoloadDumpInterface
{
    public function __invoke(string $composerPath): void
    {
        $command = sprintf('%s dump-autoload', $composerPath);
        system($command);
    }
}
