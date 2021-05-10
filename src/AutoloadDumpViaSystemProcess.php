<?php

declare(strict_types=1);

namespace Laminas\ComposerAutoloading;

use function sprintf;

final class AutoloadDumpViaSystemProcess implements AutoloadDumpInterface
{
    /**
     * @internal
     *
     * @var callable
     */
    public $systemCommand = 'system';

    public function __invoke(string $composerPath): void
    {
        $command       = sprintf('%s dump-autoload', $composerPath);
        $systemCommand = $this->systemCommand;
        $systemCommand($command);
    }
}
