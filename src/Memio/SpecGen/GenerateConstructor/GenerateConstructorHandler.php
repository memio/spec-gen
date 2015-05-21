<?php

/*
 * This file is part of the memio/spec-gen package.
 *
 * (c) Loïc Chardonnet <loic.chardonnet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Memio\SpecGen\GenerateConstructor;

use Memio\Model\File;
use Memio\Model\FullyQualifiedName;
use Memio\Model\Method;
use Memio\Model\Object;
use Memio\Model\Property;
use Memio\SpecGen\CommandBus\Command;
use Memio\SpecGen\CommandBus\CommandHandler;
use Memio\SpecGen\Marshaller\VariableArgumentMarshaller;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class GenerateConstructorHandler implements CommandHandler
{
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var VariableArgumentMarshaller
     */
    private $variableArgumentMarshaller;

    /**
     * @param EventDispatcherInterface   $eventDispatcher
     * @param VariableArgumentMarshaller $variableArgumentMarshaller
     */
    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        VariableArgumentMarshaller $variableArgumentMarshaller
    ) {
        $this->eventDispatcher = $eventDispatcher;
        $this->variableArgumentMarshaller = $variableArgumentMarshaller;
    }

    /**
     * {@inheritDoc}
     */
    public function supports(Command $command)
    {
        return $command instanceof GenerateConstructor;
    }

    /**
     * {@inheritDoc}
     */
    public function handle(Command $command)
    {
        $method = new Method($command->methodName);
        $object = Object::make($command->fullyQualifiedName)->addMethod($method);
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
        }
        $generatedConstructor = new GeneratedConstructor($file);
        $this->eventDispatcher->dispatch(GeneratedConstructor::EVENT_NAME, $generatedConstructor);
    }

    /**
     * @param File               $file
     * @param FullyQualifiedName $fullyQualifiedName
     *
     * @return bool
     */
    private function shouldAddUseStatement(File $file, FullyQualifiedName $fullyQualifiedName)
    {
        $type = $fullyQualifiedName->getFullyQualifiedName();
        $nonObjectTypes = array('string', 'bool', 'int', 'double', 'callable', 'resource', 'array', 'null', 'mixed');
        if (in_array($type, $nonObjectTypes, true)) {
            return false;
        }
        if ($fullyQualifiedName->getNamespace() === $file->getNamespace()) {
            return false;
        }
        foreach ($file->allFullyQualifiedNames() as $fullyQualifiedName) {
            if ($fullyQualifiedName->getFullyQualifiedName() === $type) {
                return false;
            }
        }

        return true;
    }
}
