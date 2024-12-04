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
use Memio\SpecGen\CodeEditor\InsertConstructor;
use Memio\SpecGen\CodeEditor\InsertConstructorHandler;
use Memio\SpecGen\CommandBus\CommandHandler;
use PhpSpec\ObjectBehavior;

class InsertConstructorHandlerSpec extends ObjectBehavior
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

    function it_supports_insert_constructor_command(InsertConstructor $insertConstructor)
    {
        $this->supports($insertConstructor)->shouldBe(true);
    }

    function it_does_not_insert_a_constructor_twice(
        Redaktilo\Editor $redaktiloEditor,
        PrettyPrinter $prettyPrinter
    ) {
        $redaktiloFile = Redaktilo\File::fromString(<<<'FILE'
<?php

namespace Vendor\Project;

class MyClass
{
    public function __construct()
    {
    }
}
FILE);
        $modelMethod = (new Model\Method('__construct'))
            ->addArgument(new Model\Argument('Vendor\Project\Dependency', 'dependency'))
            ->addArgument(new Model\Argument('string', 'filename'))
        ;
        $insertConstructor = new InsertConstructor($redaktiloFile, $modelMethod);

        $redaktiloEditor->hasBelow($redaktiloFile, InsertConstructorHandler::CONSTRUCTOR, 0)->willReturn(true);
        $prettyPrinter->generateCode($modelMethod)->shouldNotBeCalled();

        $this->handle($insertConstructor);
    }

    function it_inserts_constructor_in_empty_class(
        Redaktilo\Editor $redaktiloEditor,
        PrettyPrinter $prettyPrinter
    ) {
        $redaktiloFile = Redaktilo\File::fromString(<<<'FILE'
<?php

namespace Vendor\Project;

class MyClass
{
}
FILE);
        $modelMethod = (new Model\Method('__construct'))
            ->addArgument(new Model\Argument('Vendor\Project\Dependency', 'dependency'))
            ->addArgument(new Model\Argument('string', 'filename'))
        ;
        $insertConstructor = new InsertConstructor($redaktiloFile, $modelMethod);

        $generatedCode =<<<'GENERATED_CODE'
    public function __construct(Dependency $dependency, string $filename)
    {
        $this->dependency = $dependency;
        $this->filename = $filename;
    }
GENERATED_CODE;
        $redaktiloEditor->hasBelow($redaktiloFile, InsertConstructorHandler::CONSTRUCTOR, 0)->willReturn(false);
        $redaktiloEditor->hasBelow($redaktiloFile, InsertConstructorHandler::METHOD, 0)->willReturn(false);
        $redaktiloEditor->jumpBelow($redaktiloFile, InsertConstructorHandler::CLASS_ENDING, 0)->shouldBeCalled();
        $redaktiloFile->setCurrentLineNumber(6);
        $prettyPrinter->generateCode($modelMethod)->willReturn($generatedCode);
        $redaktiloEditor->insertAbove($redaktiloFile, $generatedCode)->shouldBeCalled();

        $this->handle($insertConstructor);
    }

    function it_inserts_constructor_in_class_with_properties_but_without_methods(
        Redaktilo\Editor $redaktiloEditor,
        PrettyPrinter $prettyPrinter
    ) {
        $redaktiloFile = Redaktilo\File::fromString(<<<'FILE'
<?php

namespace Vendor\Project;

class MyClass
{
    private $property;
}
FILE);
        $modelMethod = (new Model\Method('__construct'))
            ->addArgument(new Model\Argument('Vendor\Project\Dependency', 'dependency'))
            ->addArgument(new Model\Argument('string', 'filename'))
        ;
        $insertConstructor = new InsertConstructor($redaktiloFile, $modelMethod);

        $generatedCode =<<<'GENERATED_CODE'
    public function __construct(Dependency $dependency, string $filename)
    {
        $this->dependency = $dependency;
        $this->filename = $filename;
    }
GENERATED_CODE;

        $redaktiloEditor->hasBelow($redaktiloFile, InsertConstructorHandler::CONSTRUCTOR, 0)->willReturn(false);
        $redaktiloEditor->hasBelow($redaktiloFile, InsertConstructorHandler::METHOD, 0)->willReturn(false);
        $redaktiloEditor->jumpBelow($redaktiloFile, InsertConstructorHandler::CLASS_ENDING, 0)->shouldBeCalled();
        $redaktiloFile->setCurrentLineNumber(7);
        $prettyPrinter->generateCode($modelMethod)->willReturn($generatedCode);
        $redaktiloEditor->insertAbove($redaktiloFile, $generatedCode)->shouldBeCalled();
        $redaktiloEditor->insertBelow($redaktiloFile, '')->shouldBeCalled();

        $this->handle($insertConstructor);
    }

    function it_inserts_constructor_in_class_without_properties_but_with_methods(
        Redaktilo\Editor $redaktiloEditor,
        PrettyPrinter $prettyPrinter
    ) {
        $redaktiloFile = Redaktilo\File::fromString(<<<'FILE'
<?php

namespace Vendor\Project;

class MyClass
{
    public function existingMethod()
    {
    }
}
FILE);
        $modelMethod = (new Model\Method('__construct'))
            ->addArgument(new Model\Argument('Vendor\Project\Dependency', 'dependency'))
            ->addArgument(new Model\Argument('string', 'filename'))
        ;
        $insertConstructor = new InsertConstructor($redaktiloFile, $modelMethod);

        $generatedCode =<<<'GENERATED_CODE'
    public function __construct(Dependency $dependency, string $filename)
    {
        $this->dependency = $dependency;
        $this->filename = $filename;
    }
GENERATED_CODE;

        $redaktiloEditor->hasBelow($redaktiloFile, InsertConstructorHandler::CONSTRUCTOR, 0)->willReturn(false);
        $redaktiloEditor->hasBelow($redaktiloFile, InsertConstructorHandler::METHOD, 0)->willReturn(true);
        $redaktiloEditor->jumpBelow($redaktiloFile, InsertConstructorHandler::METHOD, 0)->shouldBeCalled();
        $redaktiloFile->setCurrentLineNumber(6);
        $redaktiloEditor->insertAbove($redaktiloFile, '')->shouldBeCalled();
        $prettyPrinter->generateCode($modelMethod)->willReturn($generatedCode);
        $redaktiloEditor->insertAbove($redaktiloFile, $generatedCode)->shouldBeCalled();

        $this->handle($insertConstructor);
    }

    function it_inserts_constructor_in_class_with_methods_and_other_stuff(
        Redaktilo\Editor $redaktiloEditor,
        PrettyPrinter $prettyPrinter
    ) {
        $redaktiloFile = Redaktilo\File::fromString(<<<'FILE'
<?php

namespace Vendor\Project;

class MyClass
{
    const CONSTANT = 42;

    public function existingMethod()
    {
    }
}
FILE);
        $modelMethod = (new Model\Method('__construct'))
            ->addArgument(new Model\Argument('Vendor\Project\Dependency', 'dependency'))
            ->addArgument(new Model\Argument('string', 'filename'))
        ;
        $insertConstructor = new InsertConstructor($redaktiloFile, $modelMethod);

        $generatedCode =<<<'GENERATED_CODE'
    public function __construct(Dependency $dependency, string $filename)
    {
        $this->dependency = $dependency;
        $this->filename = $filename;
    }
GENERATED_CODE;

        $redaktiloEditor->hasBelow($redaktiloFile, InsertConstructorHandler::CONSTRUCTOR, 0)->willReturn(false);
        $redaktiloEditor->hasBelow($redaktiloFile, InsertConstructorHandler::METHOD, 0)->willReturn(true);
        $redaktiloEditor->jumpBelow($redaktiloFile, InsertConstructorHandler::METHOD, 0)->shouldBeCalled();
        $redaktiloFile->setCurrentLineNumber(11);
        $redaktiloEditor->insertAbove($redaktiloFile, '')->shouldBeCalled();
        $prettyPrinter->generateCode($modelMethod)->willReturn($generatedCode);
        $redaktiloEditor->insertAbove($redaktiloFile, $generatedCode)->shouldBeCalled();
        $redaktiloEditor->insertBelow($redaktiloFile, '')->shouldBeCalled();

        $this->handle($insertConstructor);
    }
}
