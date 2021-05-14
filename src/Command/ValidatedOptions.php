<?php

declare(strict_types=1);

namespace Laminas\ComposerAutoloading\Command;

use Laminas\ComposerAutoloading\Composer;

/** @psalm-immutable */
final class ValidatedOptions
{
    /** @var Composer */
    public $composer;

    /** @psalm-var non-empty-string */
    public $composerPath;

    /** @psalm-var non-empty-string */
    public $module;

    /** @psalm-var non-empty-string */
    public $modulesPath;

    /** @psalm-var non-empty-string */
    public $projectPath;

    /** @psalm-var Composer::AUTOLOADER_PSR* */
    public $type;

    /**
     * @psalm-param non-empty-string $module
     * @psalm-param non-empty-string $composerPath
     * @psalm-param non-empty-string $projectPath
     * @psalm-param non-empty-string $modulesPath
     * @psalm-param Composer::AUTOLOADER_PSR* $type
     */
    public function __construct(
        string $module,
        string $composerPath,
        string $projectPath,
        string $modulesPath,
        string $type,
        Composer $composer
    ) {
        $this->module       = $module;
        $this->composerPath = $composerPath;
        $this->projectPath  = $projectPath;
        $this->modulesPath  = $modulesPath;
        $this->type         = $type;
        $this->composer     = $composer;
    }
}
