<?php

/*
 * This file is part of the memio/spec-gen package.
 *
 * (c) Loïc Chardonnet <loic.chardonnet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
