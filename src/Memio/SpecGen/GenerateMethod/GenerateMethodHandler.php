<?php

/*
 * This file is part of the memio/spec-gen package.
 *
 * (c) Loïc Chardonnet <loic.chardonnet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Memio\SpecGen\GenerateMethod;

use Memio\Model\File;
use Memio\Model\FullyQualifiedName;
use Memio\Model\Method;
use Memio\Model\Object;
use Memio\SpecGen\CommandBus\Command;
use Memio\SpecGen\CommandBus\CommandHandler;
use Memio\SpecGen\Marshaller\VariableArgumentMarshaller;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class GenerateMethodHandler implements CommandHandler
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
        return $command instanceof GenerateMethod;
    }

    /**
     * {@inheritDoc}
     */
    public function handle(Command $command)
    {
        $method = new Method($command->methodName);
        $file = File::make($command->fileName)
            ->setStructure(Object::make($command->fullyQualifiedName)
                ->addMethod($method)
            )
        ;
        $arguments = $this->variableArgumentMarshaller->marshal($command->arguments);
        foreach ($arguments as $argument) {
            $fullyQualifiedName = new FullyQualifiedName($argument->getType());
            if ($this->shouldAddUseStatement($file, $fullyQualifiedName)) {
                $file->addFullyQualifiedName($fullyQualifiedName);
            }
            $method->addArgument($argument);
        }
        $generatedMethod = new GeneratedMethod($file);
        $this->eventDispatcher->dispatch(GeneratedMethod::EVENT_NAME, $generatedMethod);
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

        return true;
    }
}
