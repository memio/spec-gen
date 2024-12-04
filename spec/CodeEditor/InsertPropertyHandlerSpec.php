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

use Gnugat\Redaktilo;
use Memio\Model;
use Memio\PrettyPrinter\PrettyPrinter;
use Memio\SpecGen\CodeEditor\InsertProperty;
use Memio\SpecGen\CodeEditor\InsertPropertyHandler;
use Memio\SpecGen\CommandBus\CommandHandler;
use PhpSpec\ObjectBehavior;

class InsertPropertyHandlerSpec extends ObjectBehavior
{
    function let(
        Redaktilo\Editor $redaktiloEditor,
        PrettyPrinter $prettyPrinter
    ) {
        $this->beConstructedWith($redaktiloEditor, $prettyPrinter);
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
        Redaktilo\Editor $redaktiloEditor
    ) {
        $redaktiloFile = Redaktilo\File::fromString(<<<'FILE'
<?php

namespace Vendor\OtherProject;

class MyClass
{
    private $dependency;
}
FILE);
        $modelProperty = new Model\Property('dependency');
        $insertProperty = new InsertProperty($redaktiloFile, $modelProperty);

        $propertyPattern = '/^    private $dependency;$/';
        $generatedCode =<<<'GENERATED_CODE'
    private $dependency;
GENERATED_CODE;

        $redaktiloEditor->hasBelow($redaktiloFile, $propertyPattern, 0)->willReturn(true);
        $redaktiloEditor->insertBelow($redaktiloFile, $generatedCode)->shouldNotBeCalled();

        $this->handle($insertProperty);
    }

    function it_inserts_property_in_empty_class(
        Redaktilo\Editor $redaktiloEditor,
        PrettyPrinter $prettyPrinter
    ) {
        $redaktiloFile = Redaktilo\File::fromString(<<<'FILE'
<?php

namespace Vendor\OtherProject;

class MyClass
{
}
FILE);
        $modelProperty = new Model\Property('dependency');
        $insertProperty = new InsertProperty($redaktiloFile, $modelProperty);

        $propertyPattern = '/^    private $dependency;$/';
        $generatedCode =<<<'GENERATED_CODE'
    private $dependency;
GENERATED_CODE;

        $redaktiloEditor->hasBelow($redaktiloFile, $propertyPattern, 0)->willReturn(false);
        $redaktiloEditor->hasBelow($redaktiloFile, InsertPropertyHandler::PROPERTY, 0)->willReturn(false);
        $redaktiloEditor->hasBelow($redaktiloFile, InsertPropertyHandler::CONSTANT, 0)->willReturn(false);
        $redaktiloEditor->jumpBelow($redaktiloFile, InsertPropertyHandler::CLASS_OPENING, 0)->shouldBeCalled();
        $prettyPrinter->generateCode($modelProperty)->willReturn($generatedCode);
        $redaktiloEditor->insertBelow($redaktiloFile, $generatedCode)->shouldBeCalled();

        $this->handle($insertProperty);
    }

    function it_inserts_property_in_class_with_properties(
        Redaktilo\Editor $redaktiloEditor,
        PrettyPrinter $prettyPrinter
    ) {
        $redaktiloFile = Redaktilo\File::fromString(<<<'FILE'
<?php

namespace Vendor\OtherProject;

class MyClass
{
    private $filename;
}
FILE);
        $modelProperty = new Model\Property('dependency');
        $insertProperty = new InsertProperty($redaktiloFile, $modelProperty);

        $propertyPattern = '/^    private $dependency;$/';
        $generatedCode =<<<'GENERATED_CODE'
    private $dependency;
GENERATED_CODE;

        $redaktiloEditor->hasBelow($redaktiloFile, $propertyPattern, 0)->willReturn(false);
        $redaktiloEditor->hasBelow($redaktiloFile, InsertPropertyHandler::PROPERTY, 0)->willReturn(true);
        $redaktiloEditor->hasBelow($redaktiloFile, InsertPropertyHandler::CONSTANT, 0)->willReturn(false);
        $redaktiloEditor->jumpBelow($redaktiloFile, InsertPropertyHandler::CLASS_ENDING, 0)->shouldBeCalled();
        $redaktiloEditor->jumpAbove($redaktiloFile, InsertPropertyHandler::PROPERTY)->shouldBeCalled();
        $redaktiloEditor->insertBelow($redaktiloFile, '')->shouldBeCalled();
        $prettyPrinter->generateCode($modelProperty)->willReturn($generatedCode);
        $redaktiloEditor->insertBelow($redaktiloFile, $generatedCode)->shouldBeCalled();

        $this->handle($insertProperty);
    }

    function it_inserts_property_in_class_with_constants(
        Redaktilo\Editor $redaktiloEditor,
        PrettyPrinter $prettyPrinter
    ) {
        $redaktiloFile = Redaktilo\File::fromString(<<<'FILE'
<?php

namespace Vendor\OtherProject;

class MyClass
{
    const CONSTANT = 42;
}
FILE);
        $modelProperty = new Model\Property('dependency');
        $insertProperty = new InsertProperty($redaktiloFile, $modelProperty);

        $propertyPattern = '/^    private $dependency;$/';
        $generatedCode =<<<'GENERATED_CODE'
    private $dependency;
GENERATED_CODE;

        $redaktiloEditor->hasBelow($redaktiloFile, $propertyPattern, 0)->willReturn(false);
        $redaktiloEditor->hasBelow($redaktiloFile, InsertPropertyHandler::PROPERTY, 0)->willReturn(false);
        $redaktiloEditor->hasBelow($redaktiloFile, InsertPropertyHandler::CONSTANT, 0)->willReturn(true);
        $redaktiloEditor->jumpBelow($redaktiloFile, InsertPropertyHandler::CLASS_ENDING, 0)->shouldBeCalled();
        $redaktiloEditor->jumpAbove($redaktiloFile, InsertPropertyHandler::CONSTANT)->shouldBeCalled();
        $redaktiloEditor->insertBelow($redaktiloFile, '')->shouldBeCalled();
        $prettyPrinter->generateCode($modelProperty)->willReturn($generatedCode);
        $redaktiloEditor->insertBelow($redaktiloFile, $generatedCode)->shouldBeCalled();

        $this->handle($insertProperty);
    }

    function it_inserts_property_in_class_with_methods(
        Redaktilo\Editor $redaktiloEditor,
        PrettyPrinter $prettyPrinter
    ) {
        $redaktiloFile = Redaktilo\File::fromString(<<<'FILE'
<?php

namespace Vendor\OtherProject;

class MyClass
{
    public function __constructy(Dependency $dependency)
    {
        $this->dependency = $dependency;
    }
}
FILE);
        $modelProperty = new Model\Property('dependency');
        $insertProperty = new InsertProperty($redaktiloFile, $modelProperty);

        $propertyPattern = '/^    private $dependency;$/';
        $generatedCode =<<<'GENERATED_CODE'
    private $dependency;
GENERATED_CODE;

        $redaktiloEditor->hasBelow($redaktiloFile, $propertyPattern, 0)->willReturn(false);
        $redaktiloEditor->hasBelow($redaktiloFile, InsertPropertyHandler::PROPERTY, 0)->willReturn(false);
        $redaktiloEditor->hasBelow($redaktiloFile, InsertPropertyHandler::CONSTANT, 0)->willReturn(false);
        $redaktiloEditor->jumpBelow($redaktiloFile, InsertPropertyHandler::CLASS_OPENING, 0)->shouldBeCalled();
        $prettyPrinter->generateCode($modelProperty)->willReturn($generatedCode);
        $redaktiloEditor->insertBelow($redaktiloFile, $generatedCode)->shouldBeCalled();
        $redaktiloFile->setCurrentLineNumber(7);
        $redaktiloEditor->insertAbove($redaktiloFile, '')->shouldBeCalled();

        $this->handle($insertProperty);
    }
}
