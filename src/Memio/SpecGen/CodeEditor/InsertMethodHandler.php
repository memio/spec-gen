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

use Gnugat\Redaktilo\Editor;
use Memio\PrettyPrinter\PrettyPrinter;
use Memio\SpecGen\CommandBus\Command;
use Memio\SpecGen\CommandBus\CommandHandler;

class InsertMethodHandler implements CommandHandler
{
    const START_OF_CLASS = '/^{$/';
    const END_OF_CLASS = '/^}$/';

    /**
     * @var Editor
     */
    private $editor;

    /**
     * @var PrettyPrinter
     */
    private $prettyPrinter;

    /**
     * @param Editor        $editor
     * @param PrettyPrinter $prettyPrinter
     */
    public function __construct(Editor $editor, PrettyPrinter $prettyPrinter)
    {
        $this->editor = $editor;
        $this->prettyPrinter = $prettyPrinter;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(Command $command)
    {
        return $command instanceof InsertMethod;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(Command $command)
    {
        $generatedCode = $this->prettyPrinter->generateCode($command->method);
        $this->editor->jumpBelow($command->file, self::END_OF_CLASS, 0);
        $this->editor->insertAbove($command->file, $generatedCode);
        $above = $command->file->getCurrentLineNumber() - 1;
        if (0 === preg_match(self::START_OF_CLASS, $command->file->getLine($above))) {
            $this->editor->insertAbove($command->file, '');
        }
    }
}
