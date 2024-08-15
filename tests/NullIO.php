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

use PhpSpec\IO\IO;

class NullIO implements IO
{
    public function write(string $message): void
    {
    }

    public function isVerbose(): bool
    {
        return false;
    }
}
