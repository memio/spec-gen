<?php

/*
 * This file is part of the memio/spec-gen package.
 *
 * (c) Loïc Faugeron <faugeron.loic@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Memio\SpecGen\Marshaller\Service;

use Memio\SpecGen\Fixtures\Types\SomeInterface;
use PhpSpec\ObjectBehavior;

class NameGuesserSpec extends ObjectBehavior
{
    function it_uses_a_generic_name_for_non_object_arguments()
    {
        $this->guess('string')->shouldBe('argument');
    }

    function it_names_object_arguments_after_their_type()
    {
        $this->guess('DateTime')->shouldBe('dateTime');
    }

    function it_removes_interface_suffix_from_object_argument_names()
    {
        $this->guess(SomeInterface::class)->shouldBe('some');
    }
}
