<?php

/*
 * This file is part of the memio/spec-gen package.
 *
 * (c) Loïc Chardonnet <loic.chardonnet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Memio\SpecGen\Marshaller\Service;

class NameGuesser
{
    /**
     * @param string $type
     *
     * @return string
     */
    public function guess($type)
    {
        $nonObjectTypes = array('string', 'bool', 'int', 'double', 'callable', 'resource', 'array', 'null', 'mixed');
        if (in_array($type, $nonObjectTypes, true)) {
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
