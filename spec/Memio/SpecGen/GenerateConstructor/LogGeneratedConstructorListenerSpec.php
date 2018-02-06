<?php

/*
 * This file is part of the memio/spec-gen package.
 *
 * (c) Loïc Faugeron <faugeron.loic@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Memio\SpecGen\GenerateConstructor;

use Memio\Model\File;
use Memio\Model\Method;
use Memio\Model\Objekt;
use Memio\Model\Property;
use Memio\SpecGen\GenerateConstructor\GeneratedConstructor;
use PhpSpec\Console\ConsoleIO;
use PhpSpec\ObjectBehavior;

class LogGeneratedConstructorListenerSpec extends ObjectBehavior
{
    const CLASS_NAME = 'MyClass';
    const METHOD_NAME = '__construct';
    const PROPERTIES_COUNT = 1;

    function let(ConsoleIO $io)
    {
        $this->beConstructedWith($io);
    }

    function it_logs_the_generated_constructor(File $file, ConsoleIO $io, Method $method, Objekt $object, Property $property)
    {
        $generatedConstructor = new GeneratedConstructor($file->getWrappedObject());
        $file->getStructure()->willReturn($object);
        $object->getName()->willReturn(self::CLASS_NAME);
        $object->allProperties()->willReturn([$property]);
        $object->allMethods()->willReturn([$method]);
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
