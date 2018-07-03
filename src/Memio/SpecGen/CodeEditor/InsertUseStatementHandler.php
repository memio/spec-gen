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
use Memio\SpecGen\CommandBus\Command;
use Memio\SpecGen\CommandBus\CommandHandler;

class InsertUseStatementHandler implements CommandHandler
{
    public const CLASS_ENDING = '}';
    public const NAME_SPACE = '/^namespace /';
    public const USE_STATEMENT = '/^use /';

    private $editor;

    public function __construct(Editor $editor)
    {
        $this->editor = $editor;
    }

    public function supports(Command $command): bool
    {
        return $command instanceof InsertUseStatement;
    }

    public function handle(Command $command): void
    {
        $namespace = $command->fullyQualifiedName->getNamespace();
        $fullyQualifiedName = $command->fullyQualifiedName->getFullyQualifiedName();
        $namespacePattern = '/^namespace '.addslashes($namespace).';$/';
        $useStatementPattern = '/^use '.addslashes($fullyQualifiedName).';$/';
        if ($this->editor->hasBelow($command->file, $namespacePattern, 0) || $this->editor->hasBelow($command->file, $useStatementPattern, 0)) {
            return;
        }
        $this->editor->jumpBelow($command->file, self::CLASS_ENDING, 0);
        if ($this->editor->hasAbove($command->file, self::USE_STATEMENT)) {
            $this->editor->jumpAbove($command->file, self::USE_STATEMENT);
        } else {
            $this->editor->jumpAbove($command->file, self::NAME_SPACE);
            $this->editor->insertBelow($command->file, '');
        }
        $generatedCode = 'use '.$fullyQualifiedName.';';
        $this->editor->insertBelow($command->file, $generatedCode);
    }
}
