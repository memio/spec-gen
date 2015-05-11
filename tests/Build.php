<?php

/*
 * This file is part of the memio/spec-gen package.
 *
 * (c) Loïc Chardonnet <loic.chardonnet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Memio\SpecGen\Tests;

use PhpSpec\ServiceContainer;
use Memio\SpecGen\MemioSpecGenExtension;

class Build
{
    /**
     * @var ServiceContainer|null
     */
    private static $serviceContainer;

    /**
     * @return ServiceContainer
     */
    public static function serviceContainer()
    {
        if (null === self::$serviceContainer) {
            self::$serviceContainer = new ServiceContainer();

            self::$serviceContainer->setShared('console.io', function (ServiceContainer $container) {
                return new NullIO();
            });

            $memioSpecGenExtension = new MemioSpecGenExtension();
            $memioSpecGenExtension->load(self::$serviceContainer);
        }

        return self::$serviceContainer;
    }

    public static function fixtures()
    {
    }
}
