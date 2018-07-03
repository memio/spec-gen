<?php

/*
 * This file is part of the memio/spec-gen package.
 *
 * (c) Loïc Faugeron <faugeron.loic@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace tests\Memio\SpecGen;

use PhpSpec\ServiceContainer;
use PhpSpec\ServiceContainer\IndexedServiceContainer;
use Memio\SpecGen\MemioSpecGenExtension;

class Build
{
    private static $serviceContainer;

    public static function serviceContainer(): ServiceContainer
    {
        if (null === self::$serviceContainer) {
            self::$serviceContainer = new IndexedServiceContainer();

            self::$serviceContainer->define('console.io', function (ServiceContainer $container) {
                return new NullIO();
            });

            $memioSpecGenExtension = new MemioSpecGenExtension();
            $memioSpecGenExtension->load(self::$serviceContainer, []);
        }

        return self::$serviceContainer;
    }

    public static function fixtures(): void
    {
    }
}
