<?php

/*
 * This file is part of the memio/spec-gen package.
 *
 * (c) Loïc Faugeron <faugeron.loic@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Memio\SpecGen\CommandBus;

/**
 * Finds the CommandHandler that supports the given command and calls it.
 */
class CommandBus
{
    private $commandHandlers = [];

    public function addCommandHandler(CommandHandler $commandHandler): void
    {
        $this->commandHandlers[] = $commandHandler;
    }

    public function handle(Command $command): void
    {
        foreach ($this->commandHandlers as $commandHandler) {
            if ($commandHandler->supports($command)) {
                $commandHandler->handle($command);
            }
        }
    }
}
