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
use PHPUnit\Framework\Assert;

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

        $arguments = $this->marshal($variables)->getWrappedObject();
        $argument = $arguments[0];
        Assert::assertSame(self::ARGUMENT_TYPE, $argument->type->name);
        Assert::assertSame(self::ARGUMENT_NAME, $argument->name);
    }
}
