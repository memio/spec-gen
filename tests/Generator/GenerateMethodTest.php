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
use PhpSpec\Locator\Resource;

class GenerateMethodTest extends GeneratorTestCase
{
    const NAME_SPACE = 'Vendor\Project';
    const CLASS_NAME = 'Vendor\Project\MyClass';

    private $methodGenerator;

    protected function setUp()
    {
        $this->methodGenerator = Build::serviceContainer()->get('code_generator.generators.method');
    }

    /**
     * @test
     */
    public function it_inserts_method_at_the_end_of_the_class()
    {
        $filename = $this->getFixtureFilename();

        $resource = $this->prophesize(Resource::class);
        $resource->getSrcFilename()->willReturn($filename);
        $resource->getSrcNamespace()->willReturn(self::NAME_SPACE);
        $resource->getSrcClassname()->willReturn(self::CLASS_NAME);

        $this->methodGenerator->generate($resource->reveal(), array(
            'name' => 'myMethod',
            'arguments' => array(),
        ));

        $this->assertExpectedCode($filename);
    }

    /**
     * @test
     */
    public function it_type_hints_arguments()
    {
        $filename = $this->getFixtureFilename();

        $resource = $this->prophesize(Resource::class);
        $resource->getSrcFilename()->willReturn($filename);
        $resource->getSrcNamespace()->willReturn(self::NAME_SPACE);
        $resource->getSrcClassname()->willReturn(self::CLASS_NAME);

        $this->methodGenerator->generate($resource->reveal(), array(
            'name' => 'myMethod',
            'arguments' => array(
                new \DateTime(),
                array(),
                'string',
            ),
        ));

        $this->assertExpectedCode($filename);
    }

    /**
     * @test
     */
    public function it_names_object_argument_after_their_type()
    {
        $filename = $this->getFixtureFilename();

        $resource = $this->prophesize(Resource::class);
        $resource->getSrcFilename()->willReturn($filename);
        $resource->getSrcNamespace()->willReturn(self::NAME_SPACE);
        $resource->getSrcClassname()->willReturn(self::CLASS_NAME);

        $this->methodGenerator->generate($resource->reveal(), array(
            'name' => 'myMethod',
            'arguments' => array(
                new \DateTime(),
            ),
        ));

        $this->assertExpectedCode($filename);
    }

    /**
     * @test
     */
    public function it_prevents_methods_to_be_longer_than_120_characters()
    {
        $filename = $this->getFixtureFilename();

        $resource = $this->prophesize(Resource::class);
        $resource->getSrcFilename()->willReturn($filename);
        $resource->getSrcNamespace()->willReturn(self::NAME_SPACE);
        $resource->getSrcClassname()->willReturn(self::CLASS_NAME);

        $this->methodGenerator->generate($resource->reveal(), array(
            'name' => 'myMethod',
            'arguments' => array(1, 2, 3, 4, 5, 6, 7, 8),
        ));

        $this->assertExpectedCode($filename);
    }
}
