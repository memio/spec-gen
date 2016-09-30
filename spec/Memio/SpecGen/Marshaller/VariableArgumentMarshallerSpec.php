<?php

/*
 * This file is part of the memio/spec-gen package.
 *
 * (c) Loïc Faugeron <faugeron.loic@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Memio\SpecGen\Marshaller;

use Memio\SpecGen\Marshaller\Service\NameGuesser;
use Memio\SpecGen\Marshaller\Service\TypeGuesser;
use PhpSpec\ObjectBehavior;

class VariableArgumentMarshallerSpec extends ObjectBehavior
{
    const ARGUMENT_TYPE = 'DateTimeInterface';
    const ARGUMENT_NAME = 'dateTime';

    function let(NameGuesser $nameGuesser, TypeGuesser $typeGuesser)
    {
        $this->beConstructedWith($nameGuesser, $typeGuesser);
    }

    function it_converts_array_of_variables_into_array_of_arguments(NameGuesser $nameGuesser, TypeGuesser $typeGuesser)
    {
        $variable = new \DateTime();
        $variables = [$variable];

        $typeGuesser->guess($variable)->willReturn(self::ARGUMENT_TYPE);
        $nameGuesser->guess(self::ARGUMENT_TYPE)->willReturn(self::ARGUMENT_NAME);

        $arguments = $this->marshal($variables);
        $argument = $arguments[0];
        $argument->getType()->shouldBe(self::ARGUMENT_TYPE);
        $argument->getName()->shouldBe(self::ARGUMENT_NAME);
    }
}
