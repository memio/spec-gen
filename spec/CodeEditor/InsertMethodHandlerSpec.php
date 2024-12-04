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
use Memio\SpecGen\CodeEditor\InsertMethod;
use Memio\SpecGen\CodeEditor\InsertMethodHandler;
use Memio\SpecGen\CommandBus\CommandHandler;
use PhpSpec\ObjectBehavior;

class InsertMethodHandlerSpec extends ObjectBehavior
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

    function it_supports_insert_method_command(InsertMethod $insertMethod)
    {
        $this->supports($insertMethod)->shouldBe(true);
    }

    function it_does_not_insert_a_method_twice(
        Redaktilo\Editor $redaktiloEditor,
        PrettyPrinter $prettyPrinter
    ) {
        $redaktiloFile = Redaktilo\File::fromString(<<<'FILE'
<?php

namespace Vendor\Project;

class MyClass
{
    public function method()
    {
    }
}
FILE);
        $modelMethod = (new Model\Method('method'))
            ->addArgument(new Model\Argument('Vendor\Project\ValueObject', 'valueObject'))
            ->addArgument(new Model\Argument('string', 'filename'))
        ;
        $insertMethod = new InsertMethod($redaktiloFile, $modelMethod);

        $methodPattern = '/^    public function method\(/';
        $redaktiloEditor->hasBelow($redaktiloFile, $methodPattern, 0)->willReturn(true);
        $prettyPrinter->generateCode($modelMethod)->shouldNotBeCalled();

        $this->handle($insertMethod);
    }

    function it_inserts_method_in_empty_class(
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
        $modelMethod = (new Model\Method('method'))
            ->addArgument(new Model\Argument('Vendor\Project\ValueObject', 'valueObject'))
            ->addArgument(new Model\Argument('string', 'filename'))
        ;
        $insertMethod = new InsertMethod($redaktiloFile, $modelMethod);

        $methodPattern = '/^    public function method\(/';
        $generatedCode =<<<'GENERATED_CODE'
    public function method(ValueObject $valueObject, string $filename)
    {
    }
GENERATED_CODE;
        $redaktiloEditor->hasBelow($redaktiloFile, $methodPattern, 0)->willReturn(false);
        $redaktiloEditor->jumpBelow($redaktiloFile, InsertMethodHandler::CLASS_ENDING, 0)->shouldBeCalled();
        $redaktiloFile->setCurrentLineNumber(6);
        $prettyPrinter->generateCode($modelMethod)->willReturn($generatedCode);
        $redaktiloEditor->insertBelow($redaktiloFile, $generatedCode)->shouldBeCalled();

        $this->handle($insertMethod);
    }

    function it_inserts_method_in_class_with_stuff(
        Redaktilo\Editor $redaktiloEditor,
        PrettyPrinter $prettyPrinter
    ) {
        $redaktiloFile = Redaktilo\File::fromString(<<<'FILE'
<?php

namespace Vendor\Project;

use Vendor\OtherProject\Dependency;

class MyClass
{
    private $dependency;

    public function __construct(Dependency $dependency)
    {
        $this->dependency = $dependency;
    }
}
FILE);
        $modelMethod = (new Model\Method('method'))
            ->addArgument(new Model\Argument('Vendor\Project\ValueObject', 'valueObject'))
            ->addArgument(new Model\Argument('string', 'filename'))
        ;
        $insertMethod = new InsertMethod($redaktiloFile, $modelMethod);

        $methodPattern = '/^    public function method\(/';
        $generatedCode =<<<'GENERATED_CODE'
    public function method(ValueObject $valueObject, string $filename)
    {
    }
GENERATED_CODE;
        $redaktiloEditor->hasBelow($redaktiloFile, $methodPattern, 0)->willReturn(false);
        $redaktiloEditor->jumpBelow($redaktiloFile, InsertMethodHandler::CLASS_ENDING, 0)->shouldBeCalled();
        $redaktiloFile->setCurrentLineNumber(14);
        $redaktiloEditor->insertBelow($redaktiloFile, '')->shouldBeCalled();
        $prettyPrinter->generateCode($modelMethod)->willReturn($generatedCode);
        $redaktiloEditor->insertBelow($redaktiloFile, $generatedCode)->shouldBeCalled();

        $this->handle($insertMethod);
    }
}
