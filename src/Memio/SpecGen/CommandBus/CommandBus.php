<?php

/*
 * This file is part of the memio/spec-gen package.
 *
 * (c) Loïc Chardonnet <loic.chardonnet@gmail.com>
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
    /**
     * @var array
     */
    private $commandHandlers = array();

    /**
     * @param CommandHandler $commandHandler
     */
    public function addCommandHandler(CommandHandler $commandHandler)
    {
        $this->commandHandlers[] = $commandHandler;
    }

    /**
     * @param Command $command
     */
    public function handle(Command $command)
    {
        foreach ($this->commandHandlers as $commandHandler) {
            if ($commandHandler->supports($command)) {
                $commandHandler->handle($command);
            }
        }
    }
}
