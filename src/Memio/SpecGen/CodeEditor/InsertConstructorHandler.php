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

class InsertConstructorHandler implements CommandHandler
{
    const CONSTRUCTOR = '/function __construct\(/';
    const METHOD = '/^    public function /';
    const CLASS_ENDING = '/^}$/';

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
        return $command instanceof InsertConstructor;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(Command $command)
    {
        if ($this->editor->hasBelow($command->file, InsertConstructorHandler::CONSTRUCTOR, 0)) {
            return;
        }
        if ($this->editor->hasBelow($command->file, InsertConstructorHandler::METHOD, 0)) {
            $this->editor->jumpBelow($command->file, InsertConstructorHandler::METHOD, 0);
            $this->editor->insertAbove($command->file, '');
        } else {
            $this->editor->jumpBelow($command->file, InsertConstructorHandler::CLASS_ENDING, 0);
        }
        $generatedCode = $this->prettyPrinter->generateCode($command->method);
        $this->editor->insertAbove($command->file, $generatedCode);
        $command->file->decrementCurrentLineNumber(1);
        $line = $command->file->getLine();
        if ('{' !== $line && '' !== $line) {
            $this->editor->insertBelow($command->file, '');
        }
    }
}
