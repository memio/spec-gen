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
use Memio\Model\Method;
use Memio\PrettyPrinter\PrettyPrinter;
use Memio\SpecGen\CodeEditor\InsertMethod;
use Memio\SpecGen\CodeEditor\InsertMethodHandler;
use PhpSpec\ObjectBehavior;

class InsertMethodHandlerSpec extends ObjectBehavior
{
    function let(Editor $editor, PrettyPrinter $prettyPrinter)
    {
        $this->beConstructedWith($editor, $prettyPrinter);
    }

    function it_is_a_command_handler()
    {
        $this->shouldImplement('Memio\SpecGen\CommandBus\CommandHandler');
    }

    function it_supports_insert_method_command(InsertMethod $insertMethod)
    {
        $this->supports($insertMethod)->shouldBe(true);
    }

    function it_inserts_the_first_method(Editor $editor, File $file, Method $method, PrettyPrinter $prettyPrinter)
    {
        $insertMethod = new InsertMethod($file->getWrappedObject(), $method->getWrappedObject());
        $generatedCode = 'abstract public function myMethod();';

        $method->getName()->willReturn('myMethod');
        $prettyPrinter->generateCode($method)->willReturn($generatedCode);
        $editor->jumpBelow($file, InsertMethodHandler::END_OF_CLASS, 0)->shouldBeCalled();
        $editor->insertAbove($file, $generatedCode)->shouldBeCalled();
        $file->getCurrentLineNumber()->willReturn(7);
        $file->getLine(6)->willReturn('{');

        $this->handle($insertMethod);
    }

    function it_inserts_methods_at_the_end_of_the_class(Editor $editor, File $file, Method $method, PrettyPrinter $prettyPrinter)
    {
        $insertMethod = new InsertMethod($file->getWrappedObject(), $method->getWrappedObject());
        $generatedCode = 'abstract public function myMethod();';

        $method->getName()->willReturn('myMethod');
        $prettyPrinter->generateCode($method)->willReturn($generatedCode);
        $editor->jumpBelow($file, InsertMethodHandler::END_OF_CLASS, 0)->shouldBeCalled();
        $editor->insertAbove($file, $generatedCode)->shouldBeCalled();
        $file->getCurrentLineNumber()->willReturn(9);
        $file->getLine(8)->willReturn('    }');
        $editor->insertAbove($file, '')->shouldBeCalled();

        $this->handle($insertMethod);
    }
}
