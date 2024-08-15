<?php

/*
 * This file is part of the memio/spec-gen package.
 *
 * (c) Loïc Faugeron <faugeron.loic@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Memio\SpecGen\CommandBus;

use Memio\SpecGen\CommandBus\Command;
use Memio\SpecGen\CommandBus\CommandHandler;
use PhpSpec\ObjectBehavior;

class CommandBusSpec extends ObjectBehavior
{
    function it_calls_appropriate_command_handler(Command $command, CommandHandler $commandHandler)
    {
        $commandHandler->supports($command)->willReturn(true);
        $commandHandler->handle($command)->shouldBeCalled();
        $this->addCommandHandler($commandHandler);

        $this->handle($command);
    }
}
