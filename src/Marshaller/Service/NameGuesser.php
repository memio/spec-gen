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

class NameGuesser
{
    private const NON_OBJECT_TYPES = [
        'string',
        'bool',
        'int',
        'double',
        'callable',
        'resource',
        'array',
        'null',
        'mixed',
    ];

    public function guess(string $type): string
    {
        if (in_array($type, self::NON_OBJECT_TYPES, true)) {
            return 'argument';
        }
        $nameSpaceBits = explode('\\', $type);
        $name = lcfirst(end($nameSpaceBits));
        $interfaceSuffixPosition = strpos($name, 'Interface');
        $hasInterfaceSuffix = (false !== $interfaceSuffixPosition && 0 < $interfaceSuffixPosition);
        if (!$hasInterfaceSuffix) {
            return $name;
        }

        return substr($name, 0, $interfaceSuffixPosition);
    }
}
