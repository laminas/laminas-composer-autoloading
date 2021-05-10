<?php

declare(strict_types=1);

namespace Laminas\ComposerAutoloading;

use function file_exists;
use function file_get_contents;
use function file_put_contents;
use function preg_match;
use function preg_replace;
use function sprintf;
use function unlink;

final class MoveModuleClassFileViaFileOperations implements MoveModuleClassFileInterface
{
    public function __invoke(string $modulePath, callable $reporter): void
    {
        $moduleClassFile = sprintf('%s/Module.php', $modulePath);
        if (! file_exists($moduleClassFile)) {
            return;
        }

        $moduleClassContents = file_get_contents($moduleClassFile);
        if (! preg_match('/\bclass Module\b/', $moduleClassContents)) {
            return;
        }

        $srcModuleClassFile = sprintf('%s/src/Module.php', $modulePath);
        if (file_exists($srcModuleClassFile)) {
            return;
        }

        $moduleClassContents = preg_replace('#(__DIR__ \. \')(/config/)#', '$1/..$2', $moduleClassContents);
        file_put_contents($srcModuleClassFile, $moduleClassContents);
        unlink($moduleClassFile);

        $reporter($moduleClassFile, $srcModuleClassFile);
    }
}
