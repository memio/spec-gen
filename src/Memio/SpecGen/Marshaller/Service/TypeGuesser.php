<?php

namespace Memio\SpecGen\Marshaller\Service;

use Memio\SpecGen\Fixtures\Types\DeepImplementation;
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
            return gettype($variable);
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
}
