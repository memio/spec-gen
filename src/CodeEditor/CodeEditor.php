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

use Gnugat\Redaktilo\Editor;
use Gnugat\Redaktilo\File;
use Memio\SpecGen\CommandBus\Command;
use Memio\SpecGen\CommandBus\CommandBus;

/**
 * Facade to enable the "Code Editor" metaphor.
 */
class CodeEditor
{
    private $commandBus;
    private $editor;

    public function __construct(CommandBus $commandBus, Editor $editor)
    {
        $this->commandBus = $commandBus;
        $this->editor = $editor;
    }

    public function open(string $filename): File
    {
        return $this->editor->open($filename);
    }

    public function save(File $file): void
    {
        $this->editor->save($file);
    }

    public function handle(Command $command): void
    {
        $this->commandBus->handle($command);
    }
}
