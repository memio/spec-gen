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

use Memio\SpecGen\CommandBus\Command;

/**
 * Data Transfer Object (DTO).
 *
 * Information given by phpspec that will allow us to generate a method.
 */
class GenerateMethod implements Command
{
    /**
     * @var string
     */
    public $fileName;

    /**
     * @var string
     */
    public $className;

    /**
     * @var string
     */
    public $methodName;

    /**
     * @var string
     */
    public $arguments;

    /**
     * @param string $fileName
     * @param string $className
     * @param string $methodName
     * @param array  $arguments
     */
    public function __construct($fileName, $className, $methodName, array $arguments)
    {
        $this->fileName = $fileName;
        $this->className = $className;
        $this->methodName = $methodName;
        $this->arguments = $arguments;
    }
}
