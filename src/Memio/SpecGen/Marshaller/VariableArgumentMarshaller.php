<?php

namespace Memio\SpecGen\Marshaller;

use Memio\SpecGen\Marshaller\Model\ArgumentCollection;

class VariableArgumentMarshaller
{
    /**
     * @param array $variables
     *
     * @return array
     */
    public function marshal(array $variables)
    {
        $argumentCollection = new ArgumentCollection();
        foreach ($variables as $variable) {
            $type = is_callable($variable) ? 'callable' : gettype($variable);
            $name = 'argument';
            if (is_object($variable)) {
                $type = get_class($variable);
                $name = lcfirst(end(explode('\\', $type)));
            }
            $argumentCollection->add($type, $name);
        }

        return $argumentCollection->all();
    }
}
