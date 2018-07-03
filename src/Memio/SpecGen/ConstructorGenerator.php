<?php

/*
 * This file is part of the memio/spec-gen package.
 *
 * (c) Loïc Faugeron <faugeron.loic@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Memio\SpecGen;

use Memio\SpecGen\CommandBus\CommandBus;
use Memio\SpecGen\GenerateConstructor\GenerateConstructor;
use PhpSpec\CodeGenerator\Generator\Generator;
use PhpSpec\Locator\Resource;

/**
 * When phpspec finds an undefined method named "__construct" in a specification, it calls this generator.
 */
class ConstructorGenerator implements Generator
{
    private $commandBus;

    public function __construct(CommandBus $commandBus)
    {
        $this->commandBus = $commandBus;
    }

    public function supports(
        Resource $resource,
        string $generation,
        array $data
    ): bool {
        return 'method' === $generation && '__construct' === $data['name'];
    }

    public function generate(Resource $resource, array $data = []): void
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
     * {@inheritdoc}
     */
    public function getPriority(): int
    {
        return 1;
    }
}
