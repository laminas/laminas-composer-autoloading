<?php

/**
 * @see       https://github.com/laminas/laminas-composer-autoloading for the canonical source repository
 * @copyright https://github.com/laminas/laminas-composer-autoloading/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-composer-autoloading/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\ComposerAutoloading;

use Laminas\ComposerAutoloading\Help;
use Laminas\Stdlib\ConsoleHelper;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

use function basename;
use function fopen;
use function realpath;
use function strpos;

class HelpTest extends TestCase
{
    public function testWritesHelpMessageToConsoleUsingCommandProvidedAtInstantiationAndResourceAtInvocation(): void
    {
        $resource = fopen('php://temp', 'wb+');

        /** @psalm-var ConsoleHelper&MockObject $console */
        $console = $this->createMock(ConsoleHelper::class);
        $console
            ->expects($this->atLeastOnce())
            ->method('writeLine')
            ->with(
                $this->callback(function (string $message): bool {
                    return false !== strpos($message, 'laminas-composer-autoloading');
                }),
                true,
                $resource
            );

        $command = new Help('laminas-composer-autoloading', $console);

        $this->assertNull($command($resource));
    }

    public function testTruncatesCommandToBasenameIfItIsARealpath(): void
    {
        $resource = fopen('php://temp', 'wb+');

        /** @psalm-var ConsoleHelper&MockObject $console */
        $console = $this->createMock(ConsoleHelper::class);
        $console
            ->expects($this->atLeastOnce())
            ->method('writeLine')
            ->with(
                $this->callback(function (string $message): bool {
                    return false !== strpos($message, basename(__FILE__));
                }),
                true,
                $resource
            );

        $command = new Help(realpath(__FILE__), $console);

        $this->assertNull($command($resource));
    }
}
