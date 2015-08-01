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

use PhpSpec\IO\IOInterface;

class NullIO implements IOInterface
{
    /**
     * {@inheritdoc}
     */
    public function write($message)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function isVerbose()
    {
        return false;
    }
}
