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
use Memio\SpecGen\CodeEditor\InsertUseStatement;
use Memio\SpecGen\CodeEditor\InsertUseStatementHandler;
use Memio\SpecGen\CommandBus\CommandHandler;
use PhpSpec\ObjectBehavior;

class InsertUseStatementHandlerSpec extends ObjectBehavior
{
    const FULLY_QUALIFIED_NAME = 'Vendor\Project\MyClass';
    const NAME_SPACE = 'Vendor\Project';
    const NAME_SPACE_PATTERN = '/^namespace Vendor\\\\Project;$/';
    const USE_STATEMENT = 'use Vendor\Project\MyClass;';
    const USE_STATEMENT_PATTERN = '/^use Vendor\\\\Project\\\\MyClass;$/';

    function let(Editor $editor)
    {
        $this->beConstructedWith($editor);
    }

    function it_is_a_command_handler()
    {
        $this->shouldImplement(CommandHandler::class);
    }

    function it_supports_insert_use_statement_command(InsertUseStatement $insertUseStatement)
    {
        $this->supports($insertUseStatement)->shouldBe(true);
    }

    function it_does_not_insert_use_statement_in_same_namespace(
        Editor $editor,
        File $file,
        FullyQualifiedName $fullyQualifiedName
    ) {
        $insertUseStatement = new InsertUseStatement($file->getWrappedObject(), $fullyQualifiedName->getWrappedObject());
        $fullyQualifiedName->getNamespace()->willReturn(self::NAME_SPACE);
        $fullyQualifiedName->getFullyQualifiedName()->willReturn(self::FULLY_QUALIFIED_NAME);

        $editor->hasBelow($file, self::NAME_SPACE_PATTERN, 0)->willReturn(true);
        $editor->insertBelow($file, self::USE_STATEMENT)->shouldNotBeCalled();

        $this->handle($insertUseStatement);
    }

    function it_does_not_insert_use_statement_twice(
        Editor $editor,
        File $file,
        FullyQualifiedName $fullyQualifiedName
    ) {
        $insertUseStatement = new InsertUseStatement($file->getWrappedObject(), $fullyQualifiedName->getWrappedObject());
        $fullyQualifiedName->getNamespace()->willReturn(self::NAME_SPACE);
        $fullyQualifiedName->getFullyQualifiedName()->willReturn(self::FULLY_QUALIFIED_NAME);

        $editor->hasBelow($file, self::NAME_SPACE_PATTERN, 0)->willReturn(false);
        $editor->hasBelow($file, self::USE_STATEMENT_PATTERN, 0)->willReturn(true);
        $editor->insertBelow($file, self::USE_STATEMENT)->shouldNotBeCalled();

        $this->handle($insertUseStatement);
    }

    function it_inserts_first_use_statement(
        Editor $editor,
        File $file,
        FullyQualifiedName $fullyQualifiedName
    ) {
        $insertUseStatement = new InsertUseStatement($file->getWrappedObject(), $fullyQualifiedName->getWrappedObject());
        $fullyQualifiedName->getNamespace()->willReturn(self::NAME_SPACE);
        $fullyQualifiedName->getFullyQualifiedName()->willReturn(self::FULLY_QUALIFIED_NAME);

        $editor->hasBelow($file, self::NAME_SPACE_PATTERN, 0)->willReturn(false);
        $editor->hasBelow($file, self::USE_STATEMENT_PATTERN, 0)->willReturn(false);
        $editor->jumpBelow($file, InsertUseStatementHandler::CLASS_ENDING, 0)->shouldBeCalled();
        $editor->hasAbove($file, InsertUseStatementHandler::USE_STATEMENT)->willReturn(false);
        $editor->jumpAbove($file, InsertUseStatementHandler::NAME_SPACE)->shouldBeCalled();
        $editor->insertBelow($file, '')->shouldBeCalled();
        $editor->insertBelow($file, self::USE_STATEMENT)->shouldBeCalled();

        $this->handle($insertUseStatement);
    }

    function it_inserts_use_statement_at_the_end_of_use_statement_block(
        Editor $editor,
        File $file,
        FullyQualifiedName $fullyQualifiedName
    ) {
        $insertUseStatement = new InsertUseStatement($file->getWrappedObject(), $fullyQualifiedName->getWrappedObject());
        $fullyQualifiedName->getNamespace()->willReturn(self::NAME_SPACE);
        $fullyQualifiedName->getFullyQualifiedName()->willReturn(self::FULLY_QUALIFIED_NAME);

        $editor->hasBelow($file, self::NAME_SPACE_PATTERN, 0)->willReturn(false);
        $editor->hasBelow($file, self::USE_STATEMENT_PATTERN, 0)->willReturn(false);
        $editor->jumpBelow($file, InsertUseStatementHandler::CLASS_ENDING, 0)->shouldBeCalled();
        $editor->hasAbove($file, InsertUseStatementHandler::USE_STATEMENT)->willReturn(true);
        $editor->jumpAbove($file, InsertUseStatementHandler::USE_STATEMENT)->shouldBeCalled();
        $editor->insertBelow($file, self::USE_STATEMENT)->shouldBeCalled();

        $this->handle($insertUseStatement);
    }
}
