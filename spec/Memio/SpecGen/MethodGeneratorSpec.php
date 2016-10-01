<?php

/*
 * This file is part of the memio/spec-gen package.
 *
 * (c) Loïc Faugeron <faugeron.loic@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Memio\SpecGen;

use Memio\SpecGen\CommandBus\CommandBus;
use Memio\SpecGen\GenerateMethod\GenerateMethod;
use PhpSpec\CodeGenerator\Generator\Generator;
use PhpSpec\Locator\Resource;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class MethodGeneratorSpec extends ObjectBehavior
{
    const FILE_NAME = 'src/Vendor/Project/MyClass.php';
    const NAME_SPACE = 'Vendor\Project';
    const CLASS_NAME = 'MyClass';
    const METHOD_NAME = 'myMethod';

    function let(CommandBus $commandBus)
    {
        $this->beConstructedWith($commandBus);
    }

    function it_is_a_generator()
    {
        $this->shouldImplement(Generator::class);
    }

    function it_supports_method_generation(Resource $resource)
    {
        $this->supports($resource, 'method', [])->shouldBe(true);
    }

    function it_calls_the_command_bus(CommandBus $commandBus, Resource $resource)
    {
        $resource->getSrcFilename()->willReturn(self::FILE_NAME);
        $resource->getSrcNamespace()->willReturn(self::NAME_SPACE);
        $resource->getSrcClassname()->willReturn(self::CLASS_NAME);
        $data = [
            'name' => self::METHOD_NAME,
            'arguments' => [],
        ];

        $command = Argument::type(GenerateMethod::class);
        $commandBus->handle($command)->shouldbeCalled();

        $this->generate($resource, $data);
    }

    function it_has_regular_priority()
    {
        $this->getPriority()->shouldBe(0);
    }
}
