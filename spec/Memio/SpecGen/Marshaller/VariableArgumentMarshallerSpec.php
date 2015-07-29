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

use Memio\SpecGen\Marshaller\Service\TypeGuesser;
use PhpSpec\ObjectBehavior;

class VariableArgumentMarshallerSpec extends ObjectBehavior
{
    function let(TypeGuesser $typeGuesser)
    {
        $this->beConstructedWith($typeGuesser);
    }

    function it_converts_array_of_variables_into_array_of_arguments(TypeGuesser $typeGuesser)
    {
        $dateTime = new \DateTime();
        $array = array();
        $string = 'string';
        $variables = array($dateTime, $array, $string);

        $typeGuesser->guess($dateTime)->willReturn('DateTime');
        $typeGuesser->guess($array)->willReturn('array');
        $typeGuesser->guess($string)->willReturn('string');

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
