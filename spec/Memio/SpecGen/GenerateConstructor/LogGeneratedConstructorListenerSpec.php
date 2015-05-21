<?php

/*
 * This file is part of the memio/spec-gen package.
 *
 * (c) Loïc Chardonnet <loic.chardonnet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Memio\SpecGen\GenerateConstructor;

use Memio\Model\File;
use Memio\Model\Method;
use Memio\Model\Object;
use Memio\Model\Property;
use Memio\SpecGen\GenerateConstructor\GeneratedConstructor;
use PhpSpec\Console\IO;
use PhpSpec\ObjectBehavior;

class LogGeneratedConstructorListenerSpec extends ObjectBehavior
{
    const CLASS_NAME = 'MyClass';
    const METHOD_NAME = '__construct';
    const PROPERTIES_COUNT = 1;

    function let(IO $io)
    {
        $this->beConstructedWith($io);
    }

    function it_logs_the_generated_constructor(File $file, IO $io, Method $method, Object $object, Property $property)
    {
        $generatedConstructor = new GeneratedConstructor($file->getWrappedObject());
        $file->getStructure()->willReturn($object);
        $object->getName()->willReturn(self::CLASS_NAME);
        $object->allProperties()->willReturn(array($property));
        $object->allMethods()->willReturn(array($method));
        $method->getName()->willReturn(self::METHOD_NAME);

        $className = self::CLASS_NAME;
        $methodName = self::METHOD_NAME;
        $propertiesCount = self::PROPERTIES_COUNT;
        $io->write(<<<OUTPUT

  <info>Generated <value>$propertiesCount</value> property for <value>$className</value>, with its constructor</info>

OUTPUT
        )->shouldBeCalled();

        $this->onGeneratedConstructor($generatedConstructor);
    }
}
