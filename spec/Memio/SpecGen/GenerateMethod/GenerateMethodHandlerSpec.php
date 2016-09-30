<?php

/*
 * This file is part of the memio/spec-gen package.
 *
 * (c) Loïc Faugeron <faugeron.loic@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Memio\SpecGen\GenerateMethod;

use Memio\SpecGen\CommandBus\CommandHandler;
use Memio\SpecGen\GenerateMethod\GenerateMethod;
use Memio\SpecGen\GenerateMethod\GeneratedMethod;
use Memio\SpecGen\Marshaller\VariableArgumentMarshaller;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class GenerateMethodHandlerSpec extends ObjectBehavior
{
    const FILE_NAME = 'src/Vendor/Project/MyClass.php';
    const CLASS_NAME = 'MyClass';
    const METHOD_NAME = 'myMethod';

    function let(EventDispatcherInterface $eventDispatcher, VariableArgumentMarshaller $variableArgumentMarshaller)
    {
        $this->beConstructedWith($eventDispatcher, $variableArgumentMarshaller);
    }

    function it_is_a_command_handler()
    {
        $this->shouldImplement(CommandHandler::class);
    }

    function it_supports_generate_method_commands(GenerateMethod $command)
    {
        $this->supports($command)->shouldBe(true);
    }

    function it_builds_a_memio_method_model(
        EventDispatcherInterface $eventDispatcher,
        VariableArgumentMarshaller $variableArgumentMarshaller
    ) {
        $variableArguments = [];
        $command = new GenerateMethod(self::FILE_NAME, self::CLASS_NAME, self::METHOD_NAME, $variableArguments);

        $variableArgumentMarshaller->marshal($variableArguments)->willReturn([]);
        $generatedMethod = Argument::type(GeneratedMethod::class);
        $eventDispatcher->dispatch(GeneratedMethod::EVENT_NAME, $generatedMethod)->shouldBeCalled();

        $this->handle($command);
    }
}
