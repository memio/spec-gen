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
        $file = new File($command->fileName);
        $method = new Method($command->methodName);
        $arguments = $this->variableArgumentMarshaller->marshal($command->arguments);
        foreach ($arguments as $argument) {
            $method->addArgument($argument);
        }
        $file->setStructure(Object::make($command->className)
            ->addMethod($method)
        );
        $generatedMethod = new GeneratedMethod($file);
        $this->eventDispatcher->dispatch(GeneratedMethod::EVENT_NAME, $generatedMethod);
    }
}
