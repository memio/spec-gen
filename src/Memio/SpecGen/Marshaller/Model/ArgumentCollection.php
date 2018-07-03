<?php

/*
 * This file is part of the memio/spec-gen package.
 *
 * (c) Loïc Faugeron <faugeron.loic@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Memio\SpecGen\Marshaller\Model;

use Memio\Model\Argument;

class ArgumentCollection
{
    private $arguments = [];
    private $nameCount = [];

    public function add(string $type, string $name): void
    {
        $this->nameCount[$name] = (isset($this->nameCount[$name]) ? $this->nameCount[$name] + 1 : 1);
        $indexedName = $name.$this->nameCount[$name];
        $isNameDuplicated = ($this->nameCount[$name] > 1);
        $this->arguments[] = new Argument($type, $isNameDuplicated ? $indexedName : $name);
        if (2 !== $this->nameCount[$name]) {
            return;
        }
        $argumentsCount = count($this->arguments);
        for ($i = 0; $i < $argumentsCount; ++$i) {
            $argument = $this->arguments[$i];
            if ($argument->getName() === $name) {
                $this->arguments[$i] = new Argument($argument->getType(), $name.'1');
                break;
            }
        }
    }

    public function all(): array
    {
        return $this->arguments;
    }
}
