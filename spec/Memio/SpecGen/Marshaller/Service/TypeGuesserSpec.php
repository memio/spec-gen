<?php

/*
 * This file is part of the memio/spec-gen package.
 *
 * (c) Loïc Chardonnet <loic.chardonnet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Memio\SpecGen\Marshaller\Service;

use Memio\SpecGen\Fixtures\Types\DeepImplementation;
use Memio\SpecGen\Fixtures\Types\DeepInterface;
use Memio\SpecGen\Fixtures\Types\NoParents;
use Memio\SpecGen\Fixtures\Types\SomeAndOtherImplementation;
use Memio\SpecGen\Fixtures\Types\SomeImplementation;
use Memio\SpecGen\Fixtures\Types\SomeInterface;
use Memio\SpecGen\Fixtures\Types\WithParents;
use PhpSpec\ObjectBehavior;

class TypeGuesserSpec extends ObjectBehavior
{
    function it_guesses_scalar_types()
    {
        $this->guess(42)->shouldBe('int');
    }

    function it_guesses_objects_without_parents()
    {
        $this->guess(new NoParents())->shouldBe('Memio\SpecGen\Fixtures\Types\NoParents');
    }

    function it_guesses_objects_with_parents()
    {
        $this->guess(new WithParents())->shouldBe('Memio\SpecGen\Fixtures\Types\WithParents');
    }

    function it_guesses_implementations_of_an_interface()
    {
        $this->guess(new SomeImplementation())->shouldBe('Memio\SpecGen\Fixtures\Types\SomeInterface');
    }

    function it_guesses_implementations_of_many_interfaces()
    {
        $this->guess(new SomeAndOtherImplementation())->shouldBe('Memio\SpecGen\Fixtures\Types\SomeInterface');
    }

    function it_guesses_implementations_of_deep_interfaces()
    {
        $this->guess(new DeepImplementation())->shouldBe('Memio\SpecGen\Fixtures\Types\DeepInterface');
    }

    function it_guesses_phpspec_doubles_of_objects_without_parents(NoParents $noParents)
    {
        $this->guess($noParents)->shouldBe('Memio\SpecGen\Fixtures\Types\NoParents');
    }

    function it_guesses_phpspec_doubles_of_objects_with_parents(WithParents $withParents)
    {
        $this->guess($withParents)->shouldBe('Memio\SpecGen\Fixtures\Types\WithParents');
    }

    function it_guesses_phpspec_doubles_of_implementations_of_an_interface(SomeImplementation $someImplementation)
    {
        $this->guess($someImplementation)->shouldBe('Memio\SpecGen\Fixtures\Types\SomeInterface');
    }

    function it_guesses_phpspec_doubles_of_implementations_of_many_interfaces(SomeAndOtherImplementation $someAndOtherImplementation)
    {
        $this->guess($someAndOtherImplementation)->shouldBe('Memio\SpecGen\Fixtures\Types\OtherInterface');
    }

    function it_guesses_phpspec_doubles_of_implementations_of_deep_interfaces(DeepImplementation $deepImplementation)
    {
        $this->guess($deepImplementation)->shouldBe('Memio\SpecGen\Fixtures\Types\SomeInterface');
    }
}
