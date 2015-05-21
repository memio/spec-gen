<?php

/*
 * This file is part of the memio/spec-gen package.
 *
 * (c) Loïc Chardonnet <loic.chardonnet@gmail.com>
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
    /**
     * @var File
     */
    public $file;

    /**
     * @var Property
     */
    public $property;

    /**
     * @param File     $file
     * @param Property $property
     */
    public function __construct(File $file, Property $property)
    {
        $this->file = $file;
        $this->property = $property;
    }
}
