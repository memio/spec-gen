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

class InsertUseStatementsHandler implements CommandHandler
{
    private $editor;
    private $insertUseStatementHandler;

    public function __construct(
        Editor $editor,
        InsertUseStatementHandler $insertUseStatementHandler
    ) {
        $this->editor = $editor;
        $this->insertUseStatementHandler = $insertUseStatementHandler;
    }

    public function supports(Command $command): bool
    {
        return $command instanceof InsertUseStatements;
    }

    public function handle(Command $command): void
    {
        foreach ($command->fullyQualifiedNames as $fullyQualifiedName) {
            $escapedFullyQualifiedClassName = addslashes($fullyQualifiedName->getFullyQualifiedName());
            if (!$this->editor->hasBelow($command->file, "/^use $escapedFullyQualifiedClassName;$/", 0)) {
                $this->insertUseStatementHandler->handle(new InsertUseStatement($command->file, $fullyQualifiedName));
            }
        }
    }
}
