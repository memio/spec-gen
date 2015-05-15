<?php

namespace Memio\SpecGen\Marshaller;

use Memio\SpecGen\Marshaller\Model\ArgumentCollection;
use Prophecy\Prophecy\ProphecySubjectInterface;

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
                if ($variable instanceof ProphecySubjectInterface) {
                    $type = ($this->getProphecyBaseType($variable));
                }
                $nameSpaceBits = explode('\\', $type);
                $name = lcfirst(end($nameSpaceBits));
            }
            $argumentCollection->add($type, $name);
        }

        return $argumentCollection->all();
    }

    /**
     * @param mixed $variable
     *
     * @return mixed|string
     */
    private function getProphecyBaseType($variable)
    {
        $typeName = get_parent_class($variable);
        if ($typeName == 'stdClass'
            && $interfaces = $this->getAllInterfaces($variable)) {
            $typeName = reset($interfaces);
        }

        return $typeName;
    }

    /**
     * @param mixed $variable
     *
     * @return array
     */
    private function getAllInterfaces($variable)
    {
        $interfaces = array_filter(
            class_implements($variable),
            function ($el) {
                return 0 !== strpos($el, 'Prophecy\\');
            }
        );

        return $interfaces;
    }
}
