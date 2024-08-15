<?php

/*
 * This file is part of the memio/spec-gen package.
 *
 * (c) Loïc Faugeron <faugeron.loic@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Memio\SpecGen\CodeEditor;

use Memio\SpecGen\CommandBus\Command;
use Memio\SpecGen\CommandBus\CommandHandler;

class InsertPropertiesHandler implements CommandHandler
{
    private $insertPropertyHandler;

    public function __construct(InsertPropertyHandler $insertPropertyHandler)
    {
        $this->insertPropertyHandler = $insertPropertyHandler;
    }

    public function supports(Command $command): bool
    {
        return $command instanceof InsertProperties;
    }

    public function handle(Command $command): void
    {
        foreach ($command->properties as $property) {
            $this->insertPropertyHandler->handle(new InsertProperty($command->file, $property));
        }
    }
}
