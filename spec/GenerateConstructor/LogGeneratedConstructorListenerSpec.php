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
    const FILE_NAME = 'src/MyClass.php';
    const CLASS_NAME = 'MyClass';
    const METHOD_NAME = '__construct';

    function let(ConsoleIO $io)
    {
        $this->beConstructedWith($io);
    }

    function it_logs_the_generated_constructor(ConsoleIO $io)
    {
        $file = new File(self::FILE_NAME);
        $file->structure = (new Objekt(self::CLASS_NAME))
            ->addMethod(new Method(self::METHOD_NAME))
        ;
        $generatedConstructor = new GeneratedConstructor($file);

        $className = self::CLASS_NAME;
        $methodName = self::METHOD_NAME;
        $io->write(<<<OUTPUT

  <info>Generated <value>{$className}#{$methodName}</value></info>

OUTPUT
        )->shouldBeCalled();

        $this->onGeneratedConstructor($generatedConstructor);
    }
}
