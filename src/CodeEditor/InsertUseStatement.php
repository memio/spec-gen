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
use Memio\Model\FullyQualifiedName;
use Memio\SpecGen\CommandBus\Command;

class InsertUseStatement implements Command
{
    public $file;
    public $fullyQualifiedName;

    public function __construct(File $file, FullyQualifiedName $fullyQualifiedName)
    {
        $this->file = $file;
        $this->fullyQualifiedName = $fullyQualifiedName;
    }
}
