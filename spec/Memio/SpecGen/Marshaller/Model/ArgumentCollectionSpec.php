<?php

namespace spec\Memio\SpecGen\Marshaller\Model;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ArgumentCollectionSpec extends ObjectBehavior
{
    function it_can_have_arguments()
    {
        $this->add('string', 'argument');

        $arguments = $this->all();
        $stringArgument = $arguments[0];
        $stringArgument->shouldHaveType('Memio\Model\Argument');
        $stringArgument->getType()->shouldBe('string');
        $stringArgument->getName()->shouldBe('argument');
    }

    function it_prevents_name_duplication()
    {
        $this->add('array', 'argument');
        $this->add('string', 'argument');

        $arguments = $this->all();
        $stringArgument = $arguments[0];
        $stringArgument->getName()->shouldBe('argument1');
        $arrayArgument = $arguments[1];
        $arrayArgument->getName()->shouldBe('argument2');
    }
}
