<?php

declare(strict_types=1);

namespace Laminas\ComposerAutoloading;

use JsonException;
use Webmozart\Assert\Assert;

use function implode;
use function in_array;
use function json_decode;
use function json_encode;
use function rtrim;
use function sprintf;

use const JSON_PRETTY_PRINT;
use const JSON_THROW_ON_ERROR;
use const JSON_UNESCAPED_SLASHES;
use const JSON_UNESCAPED_UNICODE;

final class Composer
{
    public const AUTOLOADER_PSR0  = 'psr-0';
    public const AUTOLOADER_PSR4  = 'psr-4';
    public const AUTOLOADER_TYPES = [
        self::AUTOLOADER_PSR4,
        self::AUTOLOADER_PSR0,
    ];

    private const COMPOSER_FILE = 'composer.json';

    /** @var AutoloadDumpInterface */
    private $autoloadDumper;

    /** @var bool */
    private $changed = false;

    /**
     * @var array
     * @psalm-var array<string, mixed>
     */
    private $composer;

    /** @var FileWriterInterface */
    private $fileWriter;

    /** @var string */
    private $composerJsonFile;

    public function __construct(
        string $projectDir,
        FileReaderInterface $fileReader,
        FileWriterInterface $fileWriter,
        AutoloadDumpInterface $autoloadDumper
    ) {
        $composerJsonFile = sprintf('%s/%s', rtrim($projectDir, '\\/'), self::COMPOSER_FILE);
        Assert::stringNotEmpty($composerJsonFile);

        $composerJsonFileContents = $fileReader($composerJsonFile);

        $composer = $this->deserializeJson($composerJsonFileContents, $composerJsonFile);

        $this->autoloadDumper   = $autoloadDumper;
        $this->composerJsonFile = $composerJsonFile;
        $this->composer         = $composer;
        $this->fileWriter       = $fileWriter;
    }

    /**
     * @psalm-param Composer::AUTOLOADER_PSR* $type
     * @psalm-param non-empty-string          $moduleName
     * @psalm-param non-empty-string          $modulePath
     */
    public function addAutoloaderEntry(string $type, string $moduleName, string $modulePath): void
    {
        if ($this->autoloadingRulesExist($type, $moduleName)) {
            return;
        }

        if (! isset($this->composer['autoload'][$type])) {
            $this->composer['autoload'][$type] = [];
        }

        $key  = $moduleName . '\\';
        $path = sprintf('%s/%s/src/', $modulePath, $moduleName);

        $this->composer['autoload'][$type][$key] = $path;
        $this->changed                           = true;
    }

    /**
     * @psalm-param Composer::AUTOLOADER_PSR* $type
     * @psalm-param non-empty-string          $moduleName
     */
    public function autoloadingRulesExist(string $type, string $moduleName): bool
    {
        $this->validateType($type);
        $key = $moduleName . '\\';

        return isset($this->composer['autoload'][$type][$key]);
    }

    /**
     * @psalm-param Composer::AUTOLOADER_PSR* $type
     * @psalm-param non-empty-string          $moduleName
     */
    public function removeAutoloaderEntry(string $type, string $moduleName): void
    {
        if (! $this->autoloadingRulesExist($type, $moduleName)) {
            return;
        }

        $key = $moduleName . '\\';
        unset($this->composer['autoload'][$type][$key]);
        $this->changed = true;
    }

    /**
     * @psalm-param non-empty-string $composerPath
     */
    public function updatePackageAndDumpAutoloader(string $composerPath): void
    {
        if (! $this->changed) {
            return;
        }

        $this->fileWriter->__invoke(
            $this->composerJsonFile,
            $this->serializeToJson()
        );
        $this->autoloadDumper->__invoke($composerPath);
    }

    /**
     * @psalm-param non-empty-string $filename
     * @psalm-return array<string, mixed>
     */
    private function deserializeJson(string $json, string $filename): array
    {
        try {
            return json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw Exception\ComposerJsonFileException::forUnparseableFile($filename, $json, $e);
        }
    }

    private function serializeToJson(): string
    {
        try {
            return json_encode(
                $this->composer,
                JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR
            ) . "\n";
        } catch (JsonException $e) {
            throw Exception\ComposerJsonFileException::forUnserializableContents($e);
        }
    }

    private function validateType(string $type): void
    {
        if (! in_array($type, self::AUTOLOADER_TYPES, true)) {
            throw new Exception\RuntimeException(sprintf(
                'Invalid autoloader type "%s" provided; must be one of [%s]',
                $type,
                implode(', ', self::AUTOLOADER_TYPES)
            ));
        }
    }
}
