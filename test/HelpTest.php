<?php

/**
 * @see       https://github.com/laminas/laminas-composer-autoloading for the canonical source repository
 * @copyright https://github.com/laminas/laminas-composer-autoloading/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-composer-autoloading/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\ComposerAutoloading;

use Laminas\ComposerAutoloading\Help;
use Laminas\Stdlib\ConsoleHelper;
use PHPUnit\Framework\TestCase;

class HelpTest extends TestCase
{
    public function testWritesHelpMessageToConsoleUsingCommandProvidedAtInstantiationAndResourceAtInvocation()
    {
        $resource = fopen('php://temp', 'wb+');

        /** @psalm-var ConsoleHelper&\PHPUnit\Framework\MockObject\MockObject $console */
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

    public function testTruncatesCommandToBasenameIfItIsARealpath()
    {
        $resource = fopen('php://temp', 'wb+');

        /** @psalm-var ConsoleHelper&\PHPUnit\Framework\MockObject\MockObject $console */
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
