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

class InsertUseStatementHandler implements CommandHandler
{
    const NAME_SPACE = '/^namespace /';
    const USE_STATEMENT = '/^use /';

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
        return $command instanceof InsertUseStatement;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(Command $command)
    {
        if (!$this->editor->hasBelow($command->file, self::USE_STATEMENT, 0)) {
            $this->editor->jumpBelow($command->file, self::NAME_SPACE, 0);
            $this->editor->insertBelow($command->file, '');
        } else {
            $lastLineNumber = $command->file->getLength() - 1;
            $command->file->setCurrentLineNumber($lastLineNumber);
            $this->editor->jumpAbove($command->file, self::USE_STATEMENT);
        }
        $fullyQualifiedClassName = $command->fullyQualifiedName->getFullyQualifiedName();
        $this->editor->insertBelow($command->file, "use $fullyQualifiedClassName;");
    }
}
