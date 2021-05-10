<?php

declare(strict_types=1);

namespace Laminas\ComposerAutoloading\Command;

use Laminas\ComposerAutoloading\AutoloadDumpInterface;
use Laminas\ComposerAutoloading\FileReaderInterface;
use Laminas\ComposerAutoloading\FileWriterInterface;
use Laminas\ComposerAutoloading\MoveModuleClassFileInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use function rtrim;
use function sprintf;

final class EnableCommand extends AbstractCommand
{
    private const HELP = <<<'END'
        Enable Composer-based autoloading for a module, using either PSR-0 or PSR-4.
        You may specify the path to Composer, if it is not on your $PATH, as well
        as the path to the modules directory, if it is non-standard.
        END;

    /** @var null|string */
    protected static $defaultName = 'composer:autoload:enable';

    /** @var MoveModuleClassFileInterface */
    private $moduleFileMover;

    public function __construct(
        FileReaderInterface $fileReader,
        FileWriterInterface $fileWriter,
        AutoloadDumpInterface $autoloadDumper,
        MoveModuleClassFileInterface $moduleFileMover
    ) {
        $this->fileReader      = $fileReader;
        $this->fileWriter      = $fileWriter;
        $this->autoloadDumper  = $autoloadDumper;
        $this->moduleFileMover = $moduleFileMover;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Enable composer-based autoloading for a module');
        $this->setHelp(self::HELP);

        $this->addArgument(
            'modulename',
            InputArgument::REQUIRED,
            'The name of the module for which to add autoloading'
        );

        $this->prepareCommonCommandOptions($this);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $options = $this->validateOptionsAndPrepareComposer($input);

        if ($options->composer->autoloadingRulesExist($options->type, $options->module)) {
            $output->writeln(sprintf(
                '<info>Autoloading rules of type %s already exist for module %s</info>',
                $options->type,
                $options->module
            ));
            return 0;
        }

        $resolvedModulesPath = sprintf('%s/%s', rtrim($options->projectPath, '\\/'), $options->modulesPath);
        $this->moduleFileMover->__invoke(
            $resolvedModulesPath,
            function (string $original, string $target) use ($output): void {
                $output->writeln(sprintf(
                    '<info>Renamed %s to %s</info>',
                    $original,
                    $target
                ));
            }
        );

        $output->writeln(sprintf(
            '<info>Adding %s autoloading rule for module %s</info>',
            $options->type,
            $options->module
        ));
        $options->composer->addAutoloaderEntry($options->type, $options->module, $options->modulesPath);

        $output->writeln('<info>Updating composer.json and dumping autoloader rules.</info>');
        $options->composer->updatePackageAndDumpAutoloader($options->composerPath);

        return 0;
    }
}
