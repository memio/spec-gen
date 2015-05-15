<?php

/*
 * This file is part of the memio/spec-gen package.
 *
 * (c) Loïc Chardonnet <loic.chardonnet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Memio\SpecGen\Editor;

use Gnugat\Redaktilo\Editor;
use Gnugat\Redaktilo\File;
use Memio\SpecGen\CommandBus\Command;
use Memio\SpecGen\CommandBus\CommandBus;

/**
 * Facade to enable the "Code Editor" metaphor
 */
class CodeEditor
{
    /**
     * @var CommandBus
     */
    private $commandBus;

    /**
     * @var Editor
     */
    private $editor;

    /**
     * @param CommandBus $commandBus
     * @param Editor     $editor
     */
    public function __construct(CommandBus $commandBus, Editor $editor)
    {
        $this->commandBus = $commandBus;
        $this->editor = $editor;
    }

    /**
     * @param string $filename
     *
     * @return File
     */
    public function open($filename)
    {
        return $this->editor->open($filename);
    }

    /**
     * @param File $file
     */
    public function save(File $file)
    {
        $this->editor->save($file);
    }

    /**
     * @param Command $command
     */
    public function handle(Command $command)
    {
        $this->commandBus->handle($command);
    }
}
