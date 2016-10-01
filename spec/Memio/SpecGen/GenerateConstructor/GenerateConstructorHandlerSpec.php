<?php

/*
 * This file is part of the memio/spec-gen package.
 *
 * (c) Loïc Faugeron <faugeron.loic@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Memio\SpecGen\GenerateConstructor;

use Memio\SpecGen\GenerateConstructor\GenerateConstructor;
use Memio\SpecGen\GenerateConstructor\GeneratedConstructor;
use Memio\SpecGen\Marshaller\VariableArgumentMarshaller;
use Memio\SpecGen\CommandBus\CommandHandler;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class GenerateConstructorHandlerSpec extends ObjectBehavior
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

    function it_supports_generate_method_commands(GenerateConstructor $command)
    {
        $this->supports($command)->shouldBe(true);
    }

    function it_builds_a_memio_method_model(
        EventDispatcherInterface $eventDispatcher,
        VariableArgumentMarshaller $variableArgumentMarshaller
    ) {
        $variableArguments = [];
        $command = new GenerateConstructor(self::FILE_NAME, self::CLASS_NAME, self::METHOD_NAME, $variableArguments);

        $variableArgumentMarshaller->marshal($variableArguments)->willReturn([]);
        $generatedConstructor = Argument::type(GeneratedConstructor::class);
        $eventDispatcher->dispatch(GeneratedConstructor::EVENT_NAME, $generatedConstructor)->shouldBeCalled();

        $this->handle($command);
    }
}
