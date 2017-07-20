<?php

/*
 * This file is part of the memio/spec-gen package.
 *
 * (c) Loïc Faugeron <faugeron.loic@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Memio\SpecGen\GenerateMethod;

use PhpSpec\IO\IO;

/**
 * As a developer using phpspec, I want to know when a method has been generated.
 *
 * Given a new method in my specification
 * When it has been generated
 * Then I should be notified
 */
class LogGeneratedMethodListener
{
    private $io;

    public function __construct(IO $io)
    {
        $this->io = $io;
    }

    public function onGeneratedMethod(GeneratedMethod $generatedMethod)
    {
        $object = $generatedMethod->file->getStructure();
        $className = $object->getName();
        $methods = $object->allMethods();
        $method = array_shift($methods); // $object should contain only one method, the generated one.
        $methodName = $method->getName();

        $this->io->write(<<<OUTPUT

  <info>Generated <value>$className#$methodName</value></info>

OUTPUT
        );
    }
}
