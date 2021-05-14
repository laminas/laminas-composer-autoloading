<?php

declare(strict_types=1);

namespace Laminas\ComposerAutoloading\Command;

use Laminas\ComposerAutoloading\AutoloadDumpInterface;
use Laminas\ComposerAutoloading\FileReaderInterface;
use Laminas\ComposerAutoloading\FileWriterInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use function sprintf;

final class DisableCommand extends AbstractCommand
{
    private const HELP = <<<'END'
        Disable Composer-based PSR-0 or PSR-4 autoloading for a module.
        You may specify the path to Composer, if it is not on your $PATH.
        END;

    /** @var null|string */
    protected static $defaultName = 'composer:autoload:disable';

    public function __construct(
        FileReaderInterface $fileReader,
        FileWriterInterface $fileWriter,
        AutoloadDumpInterface $autoloadDumper
    ) {
        $this->fileReader     = $fileReader;
        $this->fileWriter     = $fileWriter;
        $this->autoloadDumper = $autoloadDumper;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Disable composer-based autoloading for a module');
        $this->setHelp(self::HELP);

        $this->addArgument(
            'modulename',
            InputArgument::REQUIRED,
            'The name of the module for which to remove autoloading'
        );

        $this->prepareCommonCommandOptions($this);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $options = $this->validateOptionsAndPrepareComposer($input);

        if (! $options->composer->autoloadingRulesExist($options->type, $options->module)) {
            $output->writeln(sprintf(
                '<info>No %s autoloading rules exist for module %s; nothing to do.</info>',
                $options->type,
                $options->module
            ));
            return 0;
        }

        $output->writeln(sprintf(
            '<info>Removing %s autoloading rule for module %s</info>',
            $options->type,
            $options->module
        ));
        $options->composer->removeAutoloaderEntry($options->type, $options->module);

        $output->writeln('<info>Updating composer.json and dumping autoloader rules.</info>');
        $options->composer->updatePackageAndDumpAutoloader($options->composerPath);

        return 0;
    }
}
