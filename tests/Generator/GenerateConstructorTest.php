<?php

/*
 * This file is part of the memio/spec-gen package.
 *
 * (c) Loïc Chardonnet <loic.chardonnet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Memio\SpecGen\Tests\Generator;

use Memio\SpecGen\Tests\Build;
use Memio\SpecGen\Tests\GeneratorTestCase;

class GenerateConstructorTest extends GeneratorTestCase
{
    const NAME_SPACE = 'Vendor\Project';
    const CLASS_NAME = 'Vendor\Project\MyClass';

    private $constructorGenerator;

    protected function setUp()
    {
        $this->constructorGenerator = Build::serviceContainer()->get('code_generator.generators.constructor');
    }

    /**
     * @test
     */
    public function it_inserts_constructor_at_the_begining_of_the_class()
    {
        $filename = $this->getFixtureFilename();

        $resource = $this->prophesize('PhpSpec\Locator\ResourceInterface');
        $resource->getSrcFilename()->willReturn($filename);
        $resource->getSrcNamespace()->willReturn(self::NAME_SPACE);
        $resource->getSrcClassname()->willReturn(self::CLASS_NAME);

        $this->constructorGenerator->generate($resource->reveal(), array(
            'name' => '__construct',
            'arguments' => array(),
        ));

        $this->assertExpectedCode($filename);
    }

    /**
     * @test
     */
    public function it_inserts_properties_with_initialization_for_each_constructor_arguments()
    {
        $filename = $this->getFixtureFilename();

        $resource = $this->prophesize('PhpSpec\Locator\ResourceInterface');
        $resource->getSrcFilename()->willReturn($filename);
        $resource->getSrcNamespace()->willReturn(self::NAME_SPACE);
        $resource->getSrcClassname()->willReturn(self::CLASS_NAME);

        $this->constructorGenerator->generate($resource->reveal(), array(
            'name' => '__construct',
            'arguments' => array(
                new \DateTime(),
            ),
        ));

        $this->assertExpectedCode($filename);
    }
}
