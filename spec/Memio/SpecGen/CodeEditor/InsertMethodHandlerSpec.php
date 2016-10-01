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
use Memio\SpecGen\CodeEditor\InsertMethod;
use Memio\SpecGen\CodeEditor\InsertMethodHandler;
use Memio\SpecGen\CommandBus\CommandHandler;
use PhpSpec\ObjectBehavior;

class InsertMethodHandlerSpec extends ObjectBehavior
{
    const METHOD_NAME = 'method';
    const METHOD_PATTERN = '/^    public function method\(/';
    const GENERATED_CODE = '    public function method() { }';

    function let(Editor $editor, PrettyPrinter $prettyPrinter)
    {
        $this->beConstructedWith($editor, $prettyPrinter);
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
        Editor $editor,
        File $file,
        Method $method,
        PrettyPrinter $prettyPrinter
    ) {
        $insertMethod = new InsertMethod($file->getWrappedObject(), $method->getWrappedObject());
        $method->getName()->willReturn(self::METHOD_NAME);

        $editor->hasBelow($file, self::METHOD_PATTERN, 0)->willReturn(true);
        $prettyPrinter->generateCode($method)->shouldNotBeCalled();

        $this->handle($insertMethod);
    }

    function it_inserts_method_in_empty_class(
        Editor $editor,
        File $file,
        Method $method,
        PrettyPrinter $prettyPrinter
    ) {
        $insertMethod = new InsertMethod($file->getWrappedObject(), $method->getWrappedObject());
        $method->getName()->willReturn(self::METHOD_NAME);

        $editor->hasBelow($file, self::METHOD_PATTERN, 0)->willReturn(false);
        $editor->jumpBelow($file, InsertMethodHandler::CLASS_ENDING, 0)->shouldBeCalled();
        $file->decrementCurrentLineNumber(1)->shouldBeCalled();
        $file->getLine()->willReturn('{');
        $prettyPrinter->generateCode($method)->willReturn(self::GENERATED_CODE);
        $editor->insertBelow($file, self::GENERATED_CODE)->shouldBeCalled();

        $this->handle($insertMethod);
    }

    function it_inserts_method_in_class_with_stuff(
        Editor $editor,
        File $file,
        Method $method,
        PrettyPrinter $prettyPrinter
    ) {
        $insertMethod = new InsertMethod($file->getWrappedObject(), $method->getWrappedObject());
        $method->getName()->willReturn(self::METHOD_NAME);

        $editor->hasBelow($file, self::METHOD_PATTERN, 0)->willReturn(false);
        $editor->jumpBelow($file, InsertMethodHandler::CLASS_ENDING, 0)->shouldBeCalled();
        $file->decrementCurrentLineNumber(1)->shouldBeCalled();
        $file->getLine()->willReturn('    }');
        $editor->insertBelow($file, '')->shouldBeCalled();
        $prettyPrinter->generateCode($method)->willReturn(self::GENERATED_CODE);
        $editor->insertBelow($file, self::GENERATED_CODE)->shouldBeCalled();

        $this->handle($insertMethod);
    }
}
