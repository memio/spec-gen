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
use Memio\SpecGen\GenerateConstructor\GenerateConstructor;
use PhpSpec\CodeGenerator\Generator\GeneratorInterface;
use PhpSpec\Locator\ResourceInterface;

/**
 * When phpspec finds an undefined method named "__construct" in a specification, it calls this generator.
 */
class ConstructorGenerator implements GeneratorInterface
{
    /**
     * @var CommandBus
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
        return 'method' === $generation && '__construct' === $data['name'];
    }

    /**
     * {@inheritDoc}
     */
    public function generate(ResourceInterface $resource, array $data = array())
    {
        $generateConstructor = new GenerateConstructor(
            $resource->getSrcFilename(),
            $resource->getSrcClassName(),
            $data['name'],
            $data['arguments']
        );
        $this->commandBus->handle($generateConstructor);
    }

    /**
     * {@inheritDoc}
     */
    public function getPriority()
    {
        return 1;
    }
}
