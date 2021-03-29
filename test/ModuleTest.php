<?php

/**
 * @see       https://github.com/laminas/laminas-composer-autoloading for the canonical source repository
 * @copyright https://github.com/laminas/laminas-composer-autoloading/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-composer-autoloading/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace LaminasTest\ComposerAutoloading;

use Laminas\ComposerAutoloading\Module;
use PHPUnit\Framework\TestCase;

class ModuleTest extends TestCase
{
    public function testConfigHasExpectedTopLevelKeys(): void
    {
        $module = new Module();
        $config = $module->getConfig();

        $this->assertArrayHasKey('service_manager', $config);
        $this->assertArrayHasKey('laminas-cli', $config);
    }
}
