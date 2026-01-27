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
use PHPUnit\Framework\Assert;

class ArgumentCollectionSpec extends ObjectBehavior
{
    function it_can_have_arguments()
    {
        $this->add('string', 'argument');

        $arguments = $this->all()->getWrappedObject();
        $stringArgument = $arguments[0];
        Assert::assertInstanceOf(Argument::class, $stringArgument);
        Assert::assertSame('string', $stringArgument->type->name);
        Assert::assertSame('argument', $stringArgument->name);
    }

    function it_prevents_name_duplication()
    {
        $this->add('array', 'argument');
        $this->add('string', 'argument');

        $arguments = $this->all()->getWrappedObject();
        $firstArgument = $arguments[0];
        Assert::assertSame('argument1', $firstArgument->name);
        $secondArgument = $arguments[1];
        Assert::assertSame('argument2', $secondArgument->name);
    }
}
