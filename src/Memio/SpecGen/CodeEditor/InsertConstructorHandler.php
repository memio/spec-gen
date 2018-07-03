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
use Memio\PrettyPrinter\PrettyPrinter;
use Memio\SpecGen\CommandBus\Command;
use Memio\SpecGen\CommandBus\CommandHandler;

class InsertConstructorHandler implements CommandHandler
{
    public const CONSTRUCTOR = '/function __construct\(/';
    public const METHOD = '/^    public function /';
    public const CLASS_ENDING = '/^}$/';

    private $editor;
    private $prettyPrinter;

    public function __construct(Editor $editor, PrettyPrinter $prettyPrinter)
    {
        $this->editor = $editor;
        $this->prettyPrinter = $prettyPrinter;
    }

    public function supports(Command $command): bool
    {
        return $command instanceof InsertConstructor;
    }

    public function handle(Command $command): void
    {
        if ($this->editor->hasBelow($command->file, self::CONSTRUCTOR, 0)) {
            return;
        }
        if ($this->editor->hasBelow($command->file, self::METHOD, 0)) {
            $this->editor->jumpBelow($command->file, self::METHOD, 0);
            $this->editor->insertAbove($command->file, '');
        } else {
            $this->editor->jumpBelow($command->file, self::CLASS_ENDING, 0);
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
