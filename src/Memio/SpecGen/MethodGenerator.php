<?php

/*
 * This file is part of the memio/spec-gen package.
 *
 * (c) Loïc Chardonnet <loic.chardonnet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Memio\SpecGen;

use Memio\SpecGen\CommandBus\CommandBus;
use Memio\SpecGen\GenerateMethod\GenerateMethod;
use PhpSpec\CodeGenerator\Generator\GeneratorInterface;
use PhpSpec\Locator\ResourceInterface;

/**
 * When phpspec finds an undefined method in a specification, it calls this generator.
 */
class MethodGenerator implements GeneratorInterface
{
    /**
     * @var GenerateMethod
     */
    private $commandBus;

    /**
     * @param CommandBus $commandBus
     */
    public function __construct(CommandBus $commandBus)
    {
        $this->commandBus = $commandBus;
    }

    /**
     * {@inheritDoc}
     */
    public function supports(ResourceInterface $resource, $generation, array $data)
    {
        return 'method' === $generation;
    }

    /**
     * {@inheritDoc}
     */
    public function generate(ResourceInterface $resource, array $data = array())
    {
        $generateMethod = new GenerateMethod(
            $resource->getSrcFilename(),
            $resource->getSrcClassName(),
            $data['name'],
            $data['arguments']
        );
        $this->commandBus->handle($generateMethod);
    }

    /**
     * {@inheritDoc}
     */
    public function getPriority()
    {
        return 0;
    }
}
