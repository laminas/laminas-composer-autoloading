<?php

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
