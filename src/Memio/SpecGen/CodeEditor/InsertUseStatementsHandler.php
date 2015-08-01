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
use Memio\SpecGen\CommandBus\Command;
use Memio\SpecGen\CommandBus\CommandHandler;

class InsertUseStatementsHandler implements CommandHandler
{
    /**
     * @var Editor
     */
    private $editor;

    /**
     * @var InsertUseStatementHandler
     */
    private $insertUseStatementHandler;

    /**
     * @param Editor                    $editor
     * @param InsertUseStatementHandler $insertUseStatementHandler
     */
    public function __construct(Editor $editor, InsertUseStatementHandler $insertUseStatementHandler)
    {
        $this->editor = $editor;
        $this->insertUseStatementHandler = $insertUseStatementHandler;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(Command $command)
    {
        return $command instanceof InsertUseStatements;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(Command $command)
    {
        foreach ($command->fullyQualifiedNames as $fullyQualifiedName) {
            $escapedFullyQualifiedClassName = addslashes($fullyQualifiedName->getFullyQualifiedName());
            if (!$this->editor->hasBelow($command->file, "/^use $escapedFullyQualifiedClassName;$/", 0)) {
                $this->insertUseStatementHandler->handle(new InsertUseStatement($command->file, $fullyQualifiedName));
            }
        }
    }
}
