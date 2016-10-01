<?php

/*
 * This file is part of the memio/spec-gen package.
 *
 * (c) Loïc Faugeron <faugeron.loic@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Memio\SpecGen\Marshaller\Model;

use Memio\Model\Argument;
use PhpSpec\ObjectBehavior;

class ArgumentCollectionSpec extends ObjectBehavior
{
    function it_can_have_arguments()
    {
        $this->add('string', 'argument');

        $arguments = $this->all();
        $stringArgument = $arguments[0];
        $stringArgument->shouldHaveType(Argument::class);
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
