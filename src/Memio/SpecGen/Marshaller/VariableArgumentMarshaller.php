<?php

/*
 * This file is part of the memio/spec-gen package.
 *
 * (c) Loïc Chardonnet <loic.chardonnet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Memio\SpecGen\Marshaller;

use Memio\SpecGen\Marshaller\Model\ArgumentCollection;
use Memio\SpecGen\Marshaller\Service\TypeGuesser;

class VariableArgumentMarshaller
{
    /**
     * @var TypeGuesser
     */
    private $typeGuesser;

    /**
     * @param TypeGuesser $typeGuesser
     */
    public function __construct(TypeGuesser $typeGuesser)
    {
        $this->typeGuesser = $typeGuesser;
    }

    /**
     * @param array $variables
     *
     * @return array
     */
    public function marshal(array $variables)
    {
        $argumentCollection = new ArgumentCollection();
        foreach ($variables as $variable) {
            $type = $this->typeGuesser->guess($variable);
            $name = 'argument';
            if (is_object($variable)) {
                $nameSpaceBits = explode('\\', $type);
                $name = lcfirst(end($nameSpaceBits));
            }
            $argumentCollection->add($type, $name);
        }

        return $argumentCollection->all();
    }
}
