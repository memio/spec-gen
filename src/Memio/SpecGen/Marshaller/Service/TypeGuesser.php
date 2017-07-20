<?php

/*
 * This file is part of the memio/spec-gen package.
 *
 * (c) Loïc Faugeron <faugeron.loic@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Memio\SpecGen\Marshaller\Service;

use Prophecy\Prophecy\ProphecySubjectInterface;
use Prophecy\Doubler\Generator\ReflectionInterface;

class TypeGuesser
{
    public function guess($variable): string
    {
        if (is_callable($variable)) {
            return 'callable';
        }
        if (!is_object($variable)) {
            return $this->getNonObjectType($variable);
        }
        $interfaces = class_implements($variable);
        unset($interfaces[ProphecySubjectInterface::class]);
        unset($interfaces[ReflectionInterface::class]);
        $interface = current($interfaces);
        if (false !== $interface) {
            return $interface;
        }
        if ($variable instanceof ProphecySubjectInterface) {
            return get_parent_class($variable);
        }

        return get_class($variable);
    }

    private function getNonObjectType($variable): string
    {
        $normalizations = [
            'boolean' => 'bool',
            'integer' => 'int',
            'NULL' => 'null',
        ];
        $type = gettype($variable);
        if (isset($normalizations[$type])) {
            $type = $normalizations[$type];
        }

        return $type;
    }
}
