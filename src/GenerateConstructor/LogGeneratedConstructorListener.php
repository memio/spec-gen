<?php

/*
 * This file is part of the memio/spec-gen package.
 *
 * (c) Loïc Faugeron <faugeron.loic@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Memio\SpecGen\GenerateConstructor;

use PhpSpec\IO\IO;

/**
 * As a developer using phpspec, I want to know when a constructor has been generated.
 *
 * Given a new constructor in my specification
 * When it has been generated
 * Then I should be notified
 */
class LogGeneratedConstructorListener
{
    private $io;

    public function __construct(IO $io)
    {
        $this->io = $io;
    }

    public function onGeneratedConstructor(
        GeneratedConstructor $generatedConstructor
    ): void {
        // GeneratedConstructor only contains one single method (the generated one).
        $className = $generatedConstructor->file->structure->fullyQualifiedName->fullyQualifiedName;
        $methodName = $generatedConstructor->file->structure->methods[0]->name;

        $this->io->write(<<<OUTPUT

  <info>Generated <value>{$className}#{$methodName}</value></info>

OUTPUT
        );
    }
}
