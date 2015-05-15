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
use Memio\Model\FullyQualifiedName;
use Memio\SpecGen\CommandBus\Command;

class InsertUseStatement implements Command
{
    /**
     * @var File
     */
    public $file;

    /**
     * @var FullyQualifiedName
     */
    public $fullyQualifiedName;

    /**
     * @param File               $file
     * @param FullyQualifiedName $fullyQualifiedName
     */
    public function __construct(File $file, FullyQualifiedName $fullyQualifiedName)
    {
        $this->file = $file;
        $this->fullyQualifiedName = $fullyQualifiedName;
    }
}
