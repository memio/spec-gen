<?php

/*
 * This file is part of the memio/spec-gen package.
 *
 * (c) Loïc Faugeron <faugeron.loic@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace tests\Memio\SpecGen\Generator;

use tests\Memio\SpecGen\Build;
use tests\Memio\SpecGen\GeneratorTestCase;
use PhpSpec\Locator\Resource;

class GenerateConstructorTest extends GeneratorTestCase
{
    private const NAME_SPACE = 'Vendor\Project';
    private const CLASS_NAME = 'Vendor\Project\MyClass';

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

        $resource = $this->prophesize(Resource::class);
        $resource->getSrcFilename()->willReturn($filename);
        $resource->getSrcNamespace()->willReturn(self::NAME_SPACE);
        $resource->getSrcClassname()->willReturn(self::CLASS_NAME);

        $this->constructorGenerator->generate($resource->reveal(), [
            'name' => '__construct',
            'arguments' => [],
        ]);

        $this->assertExpectedCode($filename);
    }

    /**
     * @test
     */
    public function it_inserts_properties_with_initialization_for_each_constructor_arguments()
    {
        $filename = $this->getFixtureFilename();

        $resource = $this->prophesize(Resource::class);
        $resource->getSrcFilename()->willReturn($filename);
        $resource->getSrcNamespace()->willReturn(self::NAME_SPACE);
        $resource->getSrcClassname()->willReturn(self::CLASS_NAME);

        $this->constructorGenerator->generate($resource->reveal(), [
            'name' => '__construct',
            'arguments' => [
                new \DateTime(),
            ],
        ]);

        $this->assertExpectedCode($filename);
    }

    /**
     * @test
     */
    public function it_prevents_constructor_to_use_a_special_class_name()
    {
        $filename = $this->getFixtureFilename();

        $resource = $this->prophesize(Resource::class);
        $resource->getSrcFilename()->willReturn($filename);
        $resource->getSrcNamespace()->willReturn(self::NAME_SPACE);
        $resource->getSrcClassname()->willReturn(self::CLASS_NAME);

        $this->constructorGenerator->generate($resource->reveal(), [
            'name' => '__construct',
            'arguments' => [
                77,
                3.14,
                'letsgoboys',
                true,
            ],
        ]);

        $this->assertExpectedCode($filename);
    }
}
