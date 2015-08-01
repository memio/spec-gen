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

use Prophecy\Prophecy\ProphecySubjectInterface;

class TypeGuesser
{
    /**
     * @param mixed $variable
     *
     * @return string
     */
    public function guess($variable)
    {
        if (is_callable($variable)) {
            return 'callable';
        }
        if (!is_object($variable)) {
            return $this->getNonObjectType($variable);
        }
        $interfaces = class_implements($variable);
        unset($interfaces['Prophecy\Prophecy\ProphecySubjectInterface']);
        unset($interfaces['Prophecy\Doubler\Generator\ReflectionInterface']);
        $interface = current($interfaces);
        if (false !== $interface) {
            return $interface;
        }
        if ($variable instanceof ProphecySubjectInterface) {
            return get_parent_class($variable);
        }

        return get_class($variable);
    }

    /**
     * @param mixed $variable
     *
     * @return string
     */
    private function getNonObjectType($variable)
    {
        $normalizations = array(
            'boolean' => 'bool',
            'integer' => 'int',
            'NULL' => 'null',
        );
        $type = gettype($variable);
        if (isset($normalizations[$type])) {
            $type = $normalizations[$type];
        }

        return $type;
    }
}
