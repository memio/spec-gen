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

class InsertPropertyHandler implements CommandHandler
{
    public const CLASS_OPENING = '/^{$/';
    public const CLASS_ENDING = '/^}$/';
    public const CONSTANT = '/^    const /';
    public const PROPERTY = '/^    private \$/';

    private $editor;
    private $prettyPrinter;

    public function __construct(Editor $editor, PrettyPrinter $prettyPrinter)
    {
        $this->editor = $editor;
        $this->prettyPrinter = $prettyPrinter;
    }

    public function supports(Command $command): bool
    {
        return $command instanceof InsertProperty;
    }

    public function handle(Command $command): void
    {
        $propertyStatement = '/^    private \$'.$command->property->getName().';$/';
        if ($this->editor->hasBelow($command->file, $propertyStatement, 0)) {
            return;
        }
        $hasAnyConstants = $this->editor->hasBelow($command->file, self::CONSTANT, 0);
        $hasAnyProperties = $this->editor->hasBelow($command->file, self::PROPERTY, 0);
        if ($hasAnyProperties) {
            $this->editor->jumpBelow($command->file, self::CLASS_ENDING, 0);
            $this->editor->jumpAbove($command->file, self::PROPERTY);
            $this->editor->insertBelow($command->file, '');
        } elseif ($hasAnyConstants) {
            $this->editor->jumpBelow($command->file, self::CLASS_ENDING, 0);
            $this->editor->jumpAbove($command->file, self::CONSTANT);
            $this->editor->insertBelow($command->file, '');
        } else {
            $this->editor->jumpBelow($command->file, self::CLASS_OPENING, 0);
        }
        $generatedProperty = $this->prettyPrinter->generateCode($command->property);
        $this->editor->insertBelow($command->file, $generatedProperty);
        $command->file->incrementCurrentLineNumber(1);
        $line = trim($command->file->getLine());
        $hasAnyMethods = ('}' !== $line && '' !== $line);
        if ($hasAnyMethods) {
            $this->editor->insertAbove($command->file, '');
        }
    }
}
