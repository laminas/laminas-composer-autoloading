<?php

/**
 * @see       https://github.com/laminas/laminas-composer-autoloading for the canonical source repository
 * @copyright https://github.com/laminas/laminas-composer-autoloading/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-composer-autoloading/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\ComposerAutoloading\Command;

class Disable extends AbstractCommand
{
    /**
     * Update composer.json autoloading rules.
     *
     * Removes autoloading rule from composer.json, and executes composer dump-autoload.
     *
     * {@inheritdoc}
     */
    protected function execute()
    {
        if (! $this->autoloadingRulesExist()) {
            return false;
        }

        $composerPackage = $this->composerPackage;
        $type = $this->type;
        $module = $this->moduleName;

        unset($composerPackage['autoload'][$type][$module . '\\']);
        if (! $composerPackage['autoload'][$type]) {
            unset($composerPackage['autoload'][$type]);

            if (! $composerPackage['autoload']) {
                unset($composerPackage['autoload']);
            }
        }

        return $composerPackage;
    }
}
