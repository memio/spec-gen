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
use Memio\Model\Property;
use Memio\PrettyPrinter\PrettyPrinter;
use Memio\SpecGen\CodeEditor\InsertProperty;
use Memio\SpecGen\CodeEditor\InsertPropertyHandler;
use Memio\SpecGen\CommandBus\CommandHandler;
use PhpSpec\ObjectBehavior;

class InsertPropertyHandlerSpec extends ObjectBehavior
{
    function let(Editor $editor, PrettyPrinter $prettyPrinter)
    {
        $this->beConstructedWith($editor, $prettyPrinter);
    }

    function it_is_a_command_handler()
    {
        $this->shouldImplement(CommandHandler::class);
    }

    function it_supports_insert_property_command(InsertProperty $insertProperty)
    {
        $this->supports($insertProperty)->shouldBe(true);
    }

    function it_does_not_insert_a_property_twice(
        Editor $editor,
        File $file,
        PrettyPrinter $prettyPrinter,
        Property $property
    ) {
        $insertProperty = new InsertProperty($file->getWrappedObject(), $property->getWrappedObject());
        $property->getName()->willReturn('property');

        $editor->hasBelow($file, '/^    private \$property;$/', 0)->willReturn(true);
        $prettyPrinter->generateCode($property)->shouldNotBeCalled();

        $this->handle($insertProperty);
    }

    function it_inserts_property_in_empty_class(
        Editor $editor,
        File $file,
        PrettyPrinter $prettyPrinter,
        Property $property
    ) {
        $insertProperty = new InsertProperty($file->getWrappedObject(), $property->getWrappedObject());
        $property->getName()->willReturn('property');

        $editor->hasBelow($file, '/^    private \$property;$/', 0)->willReturn(false);
        $editor->hasBelow($file, InsertPropertyHandler::PROPERTY, 0)->willReturn(false);
        $editor->hasBelow($file, InsertPropertyHandler::CONSTANT, 0)->willReturn(false);
        $editor->jumpBelow($file, InsertPropertyHandler::CLASS_OPENING, 0)->shouldBeCalled();
        $prettyPrinter->generateCode($property)->willReturn('    private $property;');
        $editor->insertBelow($file, '    private $property;')->shouldBeCalled();
        $file->incrementCurrentLineNumber(1)->shouldBeCalled();
        $file->getLine()->willReturn('}');

        $this->handle($insertProperty);
    }

    function it_inserts_property_in_class_with_properties(
        Editor $editor,
        File $file,
        PrettyPrinter $prettyPrinter,
        Property $property
    ) {
        $insertProperty = new InsertProperty($file->getWrappedObject(), $property->getWrappedObject());
        $property->getName()->willReturn('property');

        $editor->hasBelow($file, '/^    private \$property;$/', 0)->willReturn(false);
        $editor->hasBelow($file, InsertPropertyHandler::PROPERTY, 0)->willReturn(true);
        $editor->hasBelow($file, InsertPropertyHandler::CONSTANT, 0)->willReturn(false);
        $editor->jumpBelow($file, InsertPropertyHandler::CLASS_ENDING, 0)->shouldBeCalled();
        $editor->jumpAbove($file, InsertPropertyHandler::PROPERTY)->shouldBeCalled();
        $editor->insertBelow($file, '')->shouldBeCalled();
        $prettyPrinter->generateCode($property)->willReturn('    private $property;');
        $editor->insertBelow($file, '    private $property;')->shouldBeCalled();
        $file->incrementCurrentLineNumber(1)->shouldBeCalled();
        $file->getLine()->willReturn('}');

        $this->handle($insertProperty);
    }

    function it_inserts_property_in_class_with_constants(
        Editor $editor,
        File $file,
        PrettyPrinter $prettyPrinter,
        Property $property
    ) {
        $insertProperty = new InsertProperty($file->getWrappedObject(), $property->getWrappedObject());
        $property->getName()->willReturn('property');

        $editor->hasBelow($file, '/^    private \$property;$/', 0)->willReturn(false);
        $editor->hasBelow($file, InsertPropertyHandler::PROPERTY, 0)->willReturn(false);
        $editor->hasBelow($file, InsertPropertyHandler::CONSTANT, 0)->willReturn(true);
        $editor->jumpBelow($file, InsertPropertyHandler::CLASS_ENDING, 0)->shouldBeCalled();
        $editor->jumpAbove($file, InsertPropertyHandler::CONSTANT)->shouldBeCalled();
        $editor->insertBelow($file, '')->shouldBeCalled();
        $prettyPrinter->generateCode($property)->willReturn('    private $property;');
        $editor->insertBelow($file, '    private $property;')->shouldBeCalled();
        $file->incrementCurrentLineNumber(1)->shouldBeCalled();
        $file->getLine()->willReturn('}');

        $this->handle($insertProperty);
    }

    function it_inserts_property_in_class_with_methods(
        Editor $editor,
        File $file,
        PrettyPrinter $prettyPrinter,
        Property $property
    ) {
        $insertProperty = new InsertProperty($file->getWrappedObject(), $property->getWrappedObject());
        $property->getName()->willReturn('property');

        $editor->hasBelow($file, '/^    private \$property;$/', 0)->willReturn(false);
        $editor->hasBelow($file, InsertPropertyHandler::PROPERTY, 0)->willReturn(false);
        $editor->hasBelow($file, InsertPropertyHandler::CONSTANT, 0)->willReturn(false);
        $editor->jumpBelow($file, InsertPropertyHandler::CLASS_OPENING, 0)->shouldBeCalled();
        $prettyPrinter->generateCode($property)->willReturn('    private $property;');
        $editor->insertBelow($file, '    private $property;')->shouldBeCalled();
        $file->incrementCurrentLineNumber(1)->shouldBeCalled();
        $file->getLine()->willReturn('    public function __construct($property)');
        $editor->insertAbove($file, '')->shouldBeCalled();

        $this->handle($insertProperty);
    }
}
