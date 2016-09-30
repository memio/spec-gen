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
use Memio\Model\Method;
use Memio\PrettyPrinter\PrettyPrinter;
use Memio\SpecGen\CodeEditor\InsertConstructor;
use Memio\SpecGen\CodeEditor\InsertConstructorHandler;
use Memio\SpecGen\CommandBus\CommandHandler;
use PhpSpec\ObjectBehavior;

class InsertConstructorHandlerSpec extends ObjectBehavior
{
    const GENERATED_CODE = '    abstract public function __construct();';

    function let(Editor $editor, PrettyPrinter $prettyPrinter)
    {
        $this->beConstructedWith($editor, $prettyPrinter);
    }

    function it_is_a_command_handler()
    {
        $this->shouldImplement(CommandHandler::class);
    }

    function it_supports_insert_constructor_command(InsertConstructor $insertConstructor)
    {
        $this->supports($insertConstructor)->shouldBe(true);
    }

    function it_does_not_insert_a_constructor_twice(
        Editor $editor,
        File $file,
        Method $method,
        PrettyPrinter $prettyPrinter
    ) {
        $insertConstructor = new InsertConstructor($file->getWrappedObject(), $method->getWrappedObject());

        $editor->hasBelow($file, InsertConstructorHandler::CONSTRUCTOR, 0)->willReturn(true);
        $prettyPrinter->generateCode($method)->shouldNotBeCalled();

        $this->handle($insertConstructor);
    }

    function it_inserts_constructor_in_empty_class(
        Editor $editor,
        File $file,
        Method $method,
        PrettyPrinter $prettyPrinter
    ) {
        $insertConstructor = new InsertConstructor($file->getWrappedObject(), $method->getWrappedObject());

        $editor->hasBelow($file, InsertConstructorHandler::CONSTRUCTOR, 0)->willReturn(false);
        $editor->hasBelow($file, InsertConstructorHandler::METHOD, 0)->willReturn(false);
        $editor->jumpBelow($file, InsertConstructorHandler::CLASS_ENDING, 0)->shouldBeCalled();
        $prettyPrinter->generateCode($method)->willReturn(self::GENERATED_CODE);
        $editor->insertAbove($file, self::GENERATED_CODE)->shouldBeCalled();
        $file->decrementCurrentLineNumber(1)->shouldBeCalled();
        $file->getLine()->willReturn('{');

        $this->handle($insertConstructor);
    }

    function it_inserts_constructor_in_class_without_methods(
        Editor $editor,
        File $file,
        Method $method,
        PrettyPrinter $prettyPrinter
    ) {
        $insertConstructor = new InsertConstructor($file->getWrappedObject(), $method->getWrappedObject());

        $editor->hasBelow($file, InsertConstructorHandler::CONSTRUCTOR, 0)->willReturn(false);
        $editor->hasBelow($file, InsertConstructorHandler::METHOD, 0)->willReturn(false);
        $editor->jumpBelow($file, InsertConstructorHandler::CLASS_ENDING, 0)->shouldBeCalled();
        $prettyPrinter->generateCode($method)->willReturn(self::GENERATED_CODE);
        $editor->insertAbove($file, self::GENERATED_CODE)->shouldBeCalled();
        $file->decrementCurrentLineNumber(1)->shouldBeCalled();
        $file->getLine()->willReturn('    private $property;');
        $editor->insertBelow($file, '')->shouldBeCalled();

        $this->handle($insertConstructor);
    }

    function it_inserts_constructor_in_class_with_only_methods(
        Editor $editor,
        File $file,
        Method $method,
        PrettyPrinter $prettyPrinter
    ) {
        $insertConstructor = new InsertConstructor($file->getWrappedObject(), $method->getWrappedObject());

        $editor->hasBelow($file, InsertConstructorHandler::CONSTRUCTOR, 0)->willReturn(false);
        $editor->hasBelow($file, InsertConstructorHandler::METHOD, 0)->willReturn(true);
        $editor->jumpBelow($file, InsertConstructorHandler::METHOD, 0)->shouldBeCalled();
        $editor->insertAbove($file, '')->shouldBeCalled();
        $prettyPrinter->generateCode($method)->willReturn(self::GENERATED_CODE);
        $editor->insertAbove($file, self::GENERATED_CODE)->shouldBeCalled();
        $file->decrementCurrentLineNumber(1)->shouldBeCalled();
        $file->getLine()->willReturn('{');

        $this->handle($insertConstructor);
    }

    function it_inserts_constructor_in_class_with_methods_and_other_stuff(
        Editor $editor,
        File $file,
        Method $method,
        PrettyPrinter $prettyPrinter
    ) {
        $insertConstructor = new InsertConstructor($file->getWrappedObject(), $method->getWrappedObject());

        $editor->hasBelow($file, InsertConstructorHandler::CONSTRUCTOR, 0)->willReturn(false);
        $editor->hasBelow($file, InsertConstructorHandler::METHOD, 0)->willReturn(true);
        $editor->jumpBelow($file, InsertConstructorHandler::METHOD, 0)->shouldBeCalled();
        $editor->insertAbove($file, '')->shouldBeCalled();
        $prettyPrinter->generateCode($method)->willReturn(self::GENERATED_CODE);
        $editor->insertAbove($file, self::GENERATED_CODE)->shouldBeCalled();
        $file->decrementCurrentLineNumber(1)->shouldBeCalled();
        $file->getLine()->willReturn('    const CONSTANT = 42;');
        $editor->insertBelow($file, '')->shouldBeCalled();

        $this->handle($insertConstructor);
    }
}
