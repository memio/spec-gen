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

use Memio\Model\File;
use Symfony\Component\EventDispatcher\Event;

/**
 * Data Transfer Object (DTO).
 *
 * The built model that describes the method.
 */
class GeneratedMethod extends Event
{
    public const EVENT_NAME = 'generated_method';

    public $file;

    public function __construct(File $file)
    {
        $this->file = $file;
    }
}
