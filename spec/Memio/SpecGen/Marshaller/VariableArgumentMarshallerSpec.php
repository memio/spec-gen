<?php

namespace spec\Memio\SpecGen\Marshaller;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class VariableArgumentMarshallerSpec extends ObjectBehavior
{
    function it_converts_array_of_variables_into_array_of_arguments()
    {
        $variables = array(new \DateTime(), array(), 'string');

        $arguments = $this->marshal($variables);
        $dateTimeArgument = $arguments[0];
        $dateTimeArgument->getType()->shouldBe('DateTime');
        $dateTimeArgument->getName()->shouldBe('dateTime');
        $arrayArgument = $arguments[1];
        $arrayArgument->getType()->shouldBe('array');
        $arrayArgument->getName()->shouldBe('argument1');
        $stringArgument = $arguments[2];
        $stringArgument->getType()->shouldBe('string');
        $stringArgument->getName()->shouldBe('argument2');
    }
}
