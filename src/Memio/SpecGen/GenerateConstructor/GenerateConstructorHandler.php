<?php

/*
 * This file is part of the memio/spec-gen package.
 *
 * (c) Loïc Faugeron <faugeron.loic@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Memio\SpecGen\GenerateConstructor;

use Memio\Model\File;
use Memio\Model\FullyQualifiedName;
use Memio\Model\Method;
use Memio\Model\Objekt;
use Memio\Model\Property;
use Memio\SpecGen\CommandBus\Command;
use Memio\SpecGen\CommandBus\CommandHandler;
use Memio\SpecGen\Marshaller\VariableArgumentMarshaller;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class GenerateConstructorHandler implements CommandHandler
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

    private $eventDispatcher;
    private $variableArgumentMarshaller;

    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        VariableArgumentMarshaller $variableArgumentMarshaller
    ) {
        $this->eventDispatcher = $eventDispatcher;
        $this->variableArgumentMarshaller = $variableArgumentMarshaller;
    }

    public function supports(Command $command): bool
    {
        return $command instanceof GenerateConstructor;
    }

    public function handle(Command $command): void
    {
        $method = new Method($command->methodName);
        $object = Objekt::make($command->fullyQualifiedName)->addMethod($method);
        $file = File::make($command->fileName)->setStructure($object);
        $arguments = $this->variableArgumentMarshaller->marshal($command->arguments);
        foreach ($arguments as $argument) {
            $argumentType = $argument->getType();
            $argumentName = $argument->getName();
            $fullyQualifiedName = new FullyQualifiedName($argumentType);
            if ($this->shouldAddUseStatement($file, $fullyQualifiedName)) {
                $file->addFullyQualifiedName($fullyQualifiedName);
            }
            $object->addProperty(new Property($argumentName));
            $method->addArgument($argument);
            $body = $method->getBody();
            if (!empty($body)) {
                $body .= "\n";
            }
            $body .= '        $this->'.$argumentName.' = $'.$argumentName.';';
            $method->setBody($body);
        }
        $generatedConstructor = new GeneratedConstructor($file);
        $this->eventDispatcher->dispatch(GeneratedConstructor::EVENT_NAME, $generatedConstructor);
    }

    private function shouldAddUseStatement(File $file, FullyQualifiedName $fullyQualifiedName): bool
    {
        $type = $fullyQualifiedName->getFullyQualifiedName();
        if (in_array($type, self::NON_OBJECT_TYPES, true)) {
            return false;
        }

        return true;
    }
}
