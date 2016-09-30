<?php

/*
 * This file is part of the memio/spec-gen package.
 *
 * (c) Loïc Faugeron <faugeron.loic@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Memio\SpecGen\CodeEditor;

use Gnugat\Redaktilo\File;
use Memio\Model\Property;
use Memio\SpecGen\CommandBus\Command;

class InsertProperty implements Command
{
    public $file;
    public $property;

    public function __construct(File $file, Property $property)
    {
        $this->file = $file;
        $this->property = $property;
    }
}
