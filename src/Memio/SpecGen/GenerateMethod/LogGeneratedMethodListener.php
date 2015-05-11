<?php

/*
 * This file is part of the memio/spec-gen package.
 *
 * (c) Loïc Chardonnet <loic.chardonnet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Memio\SpecGen\GenerateMethod;

use PhpSpec\IO\IOInterface;

/**
 * As a developer using phpspec, I want to know when a method has been generated.
 *
 * Given a new method in my specification
 * When it has been generated
 * Then I should be notified
 */
class LogGeneratedMethodListener
{
    /**
     * @var IOInterface
     */
    private $io;

    /**
     * @param IOInterface $io
     */
    public function __construct(IOInterface $io)
    {
        $this->io = $io;
    }

    /**
     * @param GeneratedMethod $generatedMethod
     */
    public function onGeneratedMethod(GeneratedMethod $generatedMethod)
    {
        $object = $generatedMethod->file->getStructure();
        $className = $object->getName();
        $method = array_shift($object->allMethods()); // $object should contain only one method, the generated one.
        $methodName = $method->getName();

        $this->io->write(<<<OUTPUT

  <info>Generated <value>$className#$methodName</value></info>

OUTPUT
        );
    }
}
