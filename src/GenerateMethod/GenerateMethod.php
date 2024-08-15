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

use Memio\SpecGen\CommandBus\Command;

/**
 * Data Transfer Object (DTO).
 *
 * Information given by phpspec that will allow us to generate a method.
 */
class GenerateMethod implements Command
{
    public $fileName;
    public $fullyQualifiedName;
    public $methodName;
    public $arguments;

    public function __construct(
        string $fileName,
        string $fullyQualifiedName,
        string $methodName,
        array $arguments
    ) {
        $this->fileName = $fileName;
        $this->fullyQualifiedName = $fullyQualifiedName;
        $this->methodName = $methodName;
        $this->arguments = $arguments;
    }
}
