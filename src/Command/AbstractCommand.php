<?php

declare(strict_types=1);

namespace Laminas\ComposerAutoloading\Command;

use Laminas\ComposerAutoloading\AutoloadDumpInterface;
use Laminas\ComposerAutoloading\Composer;
use Laminas\ComposerAutoloading\Exception\RuntimeException;
use Laminas\ComposerAutoloading\FileReaderInterface;
use Laminas\ComposerAutoloading\FileWriterInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Webmozart\Assert\Assert;

use function getcwd;
use function implode;
use function is_dir;
use function realpath;
use function sprintf;

abstract class AbstractCommand extends Command
{
    /** @var AutoloadDumpInterface */
    protected $autoloadDumper;

    /** @var FileReaderInterface */
    protected $fileReader;

    /** @var FileWriterInterface */
    protected $fileWriter;

    protected function prepareCommonCommandOptions(Command $command): void
    {
        $command->addOption(
            'composer',
            'c',
            InputOption::VALUE_REQUIRED,
            'Full path to the composer binary',
            'composer'
        );

        $command->addOption(
            'type',
            't',
            InputOption::VALUE_REQUIRED,
            'Autoloading type to use; one of psr-0 or psr-4'
        );

        $command->addOption(
            'project-path',
            'p',
            InputOption::VALUE_REQUIRED,
            'Path to project, if not current working directory',
            realpath(getcwd())
        );

        $command->addOption(
            'modules-path',
            'm',
            InputOption::VALUE_REQUIRED,
            'Path to the modules directory',
            'module'
        );
    }

    /**
     * Determine the autoloading type for the module.
     *
     * Call this if the autoloading type was not passed as a flag.
     *
     * Introspects the module tree to determine if PSR-0 or PSR-4 is being used.
     *
     * @psalm-return Composer::AUTOLOADER_PSR*
     * @throws RuntimeException If unable to autodetermine autoloader type.
     */
    protected function autodiscoverModuleType(
        string $projectPath,
        string $modulePath,
        string $moduleName
    ): string {
        $basePath = sprintf('%s/%s/%s', $projectPath, $modulePath, $moduleName);
        $psr0Spec = sprintf('%s/src/%s', $basePath, $moduleName);
        if (is_dir($psr0Spec)) {
            return Composer::AUTOLOADER_PSR0;
        }

        $psr4Spec = sprintf('%s/src', $basePath);
        if (is_dir($psr4Spec)) {
            return Composer::AUTOLOADER_PSR4;
        }

        throw new RuntimeException(sprintf(
            'Unable to determine autoloading type (looking in %s)',
            $basePath
        ));
    }

    protected function validateOptionsAndPrepareComposer(InputInterface $input): ValidatedOptions
    {
        $module       = $input->getArgument('modulename');
        $composerPath = $input->getOption('composer');
        $projectPath  = $input->getOption('project-path');
        $modulesPath  = $input->getOption('modules-path');

        Assert::stringNotEmpty($module, 'A non-empty string is required for the <module> name');
        Assert::stringNotEmpty($composerPath, 'A non-empty string is required for the --composer executable');
        Assert::stringNotEmpty($projectPath, '--project-path must be a directory');
        Assert::directory($projectPath, '--project-path must be a directory');
        Assert::stringNotEmpty($modulesPath, 'A non-empty string is required for the --modules-path');

        $type = $input->getOption('type') ?: $this->autodiscoverModuleType(
            $projectPath,
            $modulesPath,
            $module
        );
        Assert::oneOf(
            $type,
            Composer::AUTOLOADER_TYPES,
            sprintf('--type must be one of [%s]', implode(', ', Composer::AUTOLOADER_TYPES))
        );

        $composer = new Composer(
            $projectPath,
            $this->fileReader,
            $this->fileWriter,
            $this->autoloadDumper
        );

        return new ValidatedOptions(
            $module,
            $composerPath,
            $projectPath,
            $modulesPath,
            $type,
            $composer
        );
    }
}
