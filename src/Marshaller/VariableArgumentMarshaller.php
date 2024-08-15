<?php

/*
 * This file is part of the memio/spec-gen package.
 *
 * (c) Loïc Faugeron <faugeron.loic@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Memio\SpecGen\Marshaller;

use Memio\SpecGen\Marshaller\Model\ArgumentCollection;
use Memio\SpecGen\Marshaller\Service\NameGuesser;
use Memio\SpecGen\Marshaller\Service\TypeGuesser;

class VariableArgumentMarshaller
{
    private $nameGuesser;
    private $typeGuesser;

    public function __construct(NameGuesser $nameGuesser, TypeGuesser $typeGuesser)
    {
        $this->nameGuesser = $nameGuesser;
        $this->typeGuesser = $typeGuesser;
    }

    public function marshal(array $variables): array
    {
        $argumentCollection = new ArgumentCollection();
        foreach ($variables as $variable) {
            $type = $this->typeGuesser->guess($variable);
            $name = $this->nameGuesser->guess($type);
            $argumentCollection->add($type, $name);
        }

        return $argumentCollection->all();
    }
}
