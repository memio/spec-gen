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

class InsertPropertyHandler implements CommandHandler
{
    const CLASS_OPENING = '/^{$/';
    const CLASS_ENDING = '/^}$/';
    const CONSTANT = '/^    const /';
    const PROPERTY = '/^    private \$/';

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
     * {@inheritDoc}
     */
    public function supports(Command $command)
    {
        return $command instanceof InsertProperty;
    }

    /**
     * {@inheritDoc}
     */
    public function handle(Command $command)
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
