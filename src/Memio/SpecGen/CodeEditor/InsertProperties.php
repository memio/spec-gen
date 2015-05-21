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
use Memio\SpecGen\CommandBus\Command;

class InsertProperties implements Command
{
    /**
     * @var File
     */
    public $file;

    /**
     * @var array
     */
    public $properties;

    /**
     * @param File  $file
     * @param array $properties
     */
    public function __construct(File $file, array $properties)
    {
        $this->file = $file;
        $this->properties = $properties;
    }
}
