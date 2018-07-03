<?php

/*
 * This file is part of the memio/spec-gen package.
 *
 * (c) Loïc Faugeron <faugeron.loic@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Memio\SpecGen\GenerateMethod;

use Memio\Model\File;
use Memio\Model\FullyQualifiedName;
use Memio\Model\Method;
use Memio\Model\Objekt;
use Memio\SpecGen\CommandBus\Command;
use Memio\SpecGen\CommandBus\CommandHandler;
use Memio\SpecGen\Marshaller\VariableArgumentMarshaller;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class GenerateMethodHandler implements CommandHandler
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
        return $command instanceof GenerateMethod;
    }

    public function handle(Command $command): void
    {
        $method = new Method($command->methodName);
        $file = File::make($command->fileName)
            ->setStructure(Objekt::make($command->fullyQualifiedName)
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

    private function shouldAddUseStatement(File $file, FullyQualifiedName $fullyQualifiedName): bool
    {
        $type = $fullyQualifiedName->getFullyQualifiedName();
        if (in_array($type, self::NON_OBJECT_TYPES, true)) {
            return false;
        }

        return true;
    }
}
