<?php

/*
 * This file is part of the memio/spec-gen package.
 *
 * (c) Loïc Chardonnet <loic.chardonnet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Memio\SpecGen\GenerateConstructor;

use PhpSpec\IO\IOInterface;

/**
 * As a developer using phpspec, I want to know when a constructor has been generated.
 *
 * Given a new constructor in my specification
 * When it has been generated
 * Then I should be notified
 */
class LogGeneratedConstructorListener
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
     * @param GeneratedConstructor $generatedConstructor
     */
    public function onGeneratedConstructor(GeneratedConstructor $generatedConstructor)
    {
        $object = $generatedConstructor->file->getStructure();
        $className = $object->getName();
        $propertiesCount = count($object->allProperties());
        $methods = $object->allMethods();
        $method = array_shift($methods); // $object should contain only one method, the generated one.

        $propertiesWord = (1 === $propertiesCount ? 'property' : 'properties');
        $this->io->write(<<<OUTPUT

  <info>Generated <value>$propertiesCount</value> $propertiesWord for <value>$className</value>, with its constructor</info>

OUTPUT
        );
    }
}
