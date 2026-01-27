<?php

/*
 * This file is part of the memio/spec-gen package.
 *
 * (c) Loïc Faugeron <faugeron.loic@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Memio\SpecGen\GenerateMethod;

use Memio\Model\File;
use Memio\Model\Method;
use Memio\Model\Objekt;
use Memio\SpecGen\GenerateMethod\GeneratedMethod;
use PhpSpec\Console\ConsoleIO;
use PhpSpec\ObjectBehavior;

class LogGeneratedMethodListenerSpec extends ObjectBehavior
{
    const CLASS_NAME = 'MyClass';
    const METHOD_NAME = 'myMethod';

    function let(ConsoleIO $io)
    {
        $this->beConstructedWith($io);
    }

    function it_logs_the_generated_method(ConsoleIO $io)
    {
        $method = new Method(self::METHOD_NAME);
        $object = (new Objekt('Vendor\Project\\'.self::CLASS_NAME))
            ->addMethod($method);
        $file = (new File('/tmp/test.php'))->setStructure($object);

        $generatedMethod = new GeneratedMethod($file);

        $className = self::CLASS_NAME;
        $methodName = self::METHOD_NAME;
        $io->write(<<<OUTPUT

  <info>Generated <value>$className#$methodName</value></info>

OUTPUT
        )->shouldBeCalled();

        $this->onGeneratedMethod($generatedMethod);
    }
}
