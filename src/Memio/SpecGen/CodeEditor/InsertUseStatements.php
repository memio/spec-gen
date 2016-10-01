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
use Memio\SpecGen\CommandBus\Command;

class InsertUseStatements implements Command
{
    public $file;
    public $fullyQualifiedNames;

    public function __construct(File $file, array $fullyQualifiedNames)
    {
        $this->file = $file;
        $this->fullyQualifiedNames = $fullyQualifiedNames;
    }
}
