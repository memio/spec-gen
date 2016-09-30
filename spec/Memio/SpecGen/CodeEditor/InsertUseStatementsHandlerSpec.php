<?php

/*
 * This file is part of the memio/spec-gen package.
 *
 * (c) Loïc Faugeron <faugeron.loic@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Memio\SpecGen\CodeEditor;

use Gnugat\Redaktilo\Editor;
use Gnugat\Redaktilo\File;
use Memio\Model\FullyQualifiedName;
use Memio\SpecGen\CodeEditor\InsertUseStatementHandler;
use Memio\SpecGen\CodeEditor\InsertUseStatements;
use Memio\SpecGen\CodeEditor\InsertUseStatement;
use Memio\SpecGen\CommandBus\CommandHandler;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class InsertUseStatementsHandlerSpec extends ObjectBehavior
{
    function let(Editor $editor, InsertUseStatementHandler $insertUseStatementHandler)
    {
        $this->beConstructedWith($editor, $insertUseStatementHandler);
    }

    function it_is_a_command_handler()
    {
        $this->shouldImplement(CommandHandler::class);
    }

    function it_supports_insert_use_statements_command(InsertUseStatements $insertUseStatements)
    {
        $this->supports($insertUseStatements)->shouldBe(true);
    }

    function it_inserts_the_same_use_statement_once(
        Editor $editor,
        File $file,
        FullyQualifiedName $fullyQualifiedName,
        InsertUseStatementHandler $insertUseStatementHandler
    ) {
        $fullyQualifiedNames = [$fullyQualifiedName->getWrappedObject()];
        $insertUseStatements = new InsertUseStatements($file->getWrappedObject(), $fullyQualifiedNames);

        $fullyQualifiedName->getFullyQualifiedName()->willReturn('Vendor\Project\MyDependency');
        $editor->hasBelow($file, '/^use Vendor\\\\Project\\\\MyDependency;$/', 0)->willReturn(false);
        $insertUseStatement = Argument::Type(InsertUseStatement::class);
        $insertUseStatementHandler->handle($insertUseStatement)->shouldBeCalled();

        $this->handle($insertUseStatements);
    }

    function it_does_not_insert_the_same_use_statement_twice(
        Editor $editor,
        File $file,
        FullyQualifiedName $fullyQualifiedName,
        InsertUseStatementHandler $insertUseStatementHandler
    ) {
        $fullyQualifiedNames = [$fullyQualifiedName->getWrappedObject()];
        $insertUseStatements = new InsertUseStatements($file->getWrappedObject(), $fullyQualifiedNames);

        $fullyQualifiedName->getFullyQualifiedName()->willReturn('Vendor\Project\MyDependency');
        $editor->hasBelow($file, '/^use Vendor\\\\Project\\\\MyDependency;$/', 0)->willReturn(true);
        $insertUseStatement = Argument::Type(InsertUseStatement::class);
        $insertUseStatementHandler->handle($insertUseStatement)->shouldNotBeCalled();

        $this->handle($insertUseStatements);
    }
}
