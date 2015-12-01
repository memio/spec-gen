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
 * Handles a specific Command.
 */
interface CommandHandler
{
    /**
     * @param Command $command
     */
    public function supports(Command $command);

    /**
     * @param Command $command
     */
    public function handle(Command $command);
}
