<?php

/*
 * This file is part of the memio/spec-gen package.
 *
 * (c) Loïc Chardonnet <loic.chardonnet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Memio\SpecGen\CodeEditor;

use Gnugat\Redaktilo\Editor;
use Gnugat\Redaktilo\File;
use Memio\Model\FullyQualifiedName;
use Memio\PrettyPrinter\PrettyPrinter;
use Memio\SpecGen\CodeEditor\InsertUseStatement;
use Memio\SpecGen\CodeEditor\InsertUseStatementHandler;
use PhpSpec\ObjectBehavior;

class InsertUseStatementHandlerSpec extends ObjectBehavior
{
    function let(Editor $editor, PrettyPrinter $prettyPrinter)
    {
        $this->beConstructedWith($editor, $prettyPrinter);
    }

    function it_is_a_command_handler()
    {
        $this->shouldImplement('Memio\SpecGen\CommandBus\CommandHandler');
    }

    function it_supports_insert_use_statement_command(InsertUseStatement $insertUseStatement)
    {
        $this->supports($insertUseStatement)->shouldBe(true);
    }

    function it_inserts_the_first_use_statement(Editor $editor, File $file, FullyQualifiedName $fullyQualifiedName, PrettyPrinter $prettyPrinter)
    {
        $insertUseStatement = new InsertUseStatement($file->getWrappedObject(), $fullyQualifiedName->getWrappedObject());
        $fullyQualifiedName->getFullyQualifiedName()->willReturn('Vendor\Project\MyDependency');

        $editor->hasBelow($file, InsertUseStatementHandler::USE_STATEMENT, 0)->willReturn(false);
        $editor->jumpBelow($file, InsertUseStatementHandler::NAME_SPACE, 0)->shouldBeCalled();
        $editor->insertBelow($file, '')->shouldBeCalled();
        $editor->insertBelow($file, "use Vendor\Project\MyDependency;")->shouldBeCalled();

        $this->handle($insertUseStatement);
    }

    function it_inserts_use_Statement_at_the_end_of_the_use_statements_block(Editor $editor, File $file, FullyQualifiedName $fullyQualifiedName, PrettyPrinter $prettyPrinter)
    {
        $insertUseStatement = new InsertUseStatement($file->getWrappedObject(), $fullyQualifiedName->getWrappedObject());
        $fullyQualifiedName->getFullyQualifiedName()->willReturn('Vendor\Project\MyDependency');

        $editor->hasBelow($file, InsertUseStatementHandler::USE_STATEMENT, 0)->willReturn(true);
        $file->getLength()->willReturn(10);
        $file->setCurrentLineNumber(9)->shouldBeCalled();
        $editor->jumpAbove($file, InsertUseStatementHandler::USE_STATEMENT)->shouldBeCalled();
        $editor->insertBelow($file, "use Vendor\Project\MyDependency;")->shouldBeCalled();

        $this->handle($insertUseStatement);
    }
}
