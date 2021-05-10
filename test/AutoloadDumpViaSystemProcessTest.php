<?php

declare(strict_types=1);

namespace LaminasTest\ComposerAutoloading;

use Laminas\ComposerAutoloading\AutoloadDumpViaSystemProcess;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

use function sprintf;

class AutoloadDumpViaSystemProcessTest extends TestCase
{
    public function testInvokesSystemCommandWithProvidedComposerWithExpectedArguments(): void
    {
        $composerPath = '/usr/local/bin/composer.phar';
        $dumper       = new AutoloadDumpViaSystemProcess();

        /** @psalm-suppress InternalProperty */
        $dumper->systemCommand = function (string $command) use ($composerPath): void {
            $expected = sprintf('%s dump-autoload', $composerPath);
            Assert::assertSame($expected, $command);
        };

        $this->assertNull($dumper($composerPath));
    }
}
