<?php

/*
 * This file is part of the memio/spec-gen package.
 *
 * (c) Loïc Chardonnet <loic.chardonnet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Memio\SpecGen;

use Memio\SpecGen\GenerateMethod\GeneratedMethod;
use PhpSpec\Extension\ExtensionInterface;
use PhpSpec\ServiceContainer;

/**
 * Adding to phpspec's Dependency Injection Container Memio SpecGen's services.
 *
 * Here we over write phpspec's generators by ours.
 */
class MemioSpecGenExtension implements ExtensionInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ServiceContainer $container)
    {
        $this->setupSharedServices($container);
        $this->setupGenerateMethodHandler($container);
        $this->setupGenerators($container);
    }

    /**
     * @param ServiceContainer $container
     */
    private function setupSharedServices(ServiceContainer $container)
    {
        $container->setShared('redaktilo.editor', function () {
            return \Gnugat\Redaktilo\EditorFactory::createEditor();
        });
        $container->setShared('memio.pretty_printer', function () {
            return \Memio\Memio\Config\Build::prettyPrinter();
        });
        $container->setShared('memio_spec_gen.event_dispatcher', function (ServiceContainer $container) {
            return new \Symfony\Component\EventDispatcher\EventDispatcher();
        });
        $container->setShared('memio_spec_gen.command_bus', function (ServiceContainer $container) {
            return new \Memio\SpecGen\CommandBus\CommandBus();
        });
    }

    /**
     * @param ServiceContainer $container
     */
    private function setupGenerateMethodHandler(ServiceContainer $container)
    {
        $eventDispatcher = $container->get('memio_spec_gen.event_dispatcher');
        $commandBus = $container->get('memio_spec_gen.command_bus');

        $insertGeneratedMethodListener = new \Memio\SpecGen\GenerateMethod\InsertGeneratedMethodListener(
            $container->get('redaktilo.editor'),
            $container->get('memio.pretty_printer')
        );
        $logGeneratedMethodListener = new \Memio\SpecGen\GenerateMethod\LogGeneratedMethodListener(
            $container->get('console.io')
        );
        $eventDispatcher->addListener(GeneratedMethod::EVENT_NAME, array($insertGeneratedMethodListener, 'onGeneratedMethod'));
        $eventDispatcher->addListener(GeneratedMethod::EVENT_NAME, array($logGeneratedMethodListener, 'onGeneratedMethod'));

        $generateMethodHandler = new \Memio\SpecGen\GenerateMethod\GenerateMethodHandler(
            $container->get('memio_spec_gen.event_dispatcher'),
            new \Memio\SpecGen\Marshaller\VariableArgumentMarshaller()
        );
        $commandBus->addCommandHandler($generateMethodHandler);
    }

    /**
     * @param ServiceContainer $container
     */
    private function setupGenerators(ServiceContainer $container)
    {
        $container->set('code_generator.generators.method', function (ServiceContainer $container) {
            return new \Memio\SpecGen\MethodGenerator($container->get('memio_spec_gen.command_bus'));
        });
    }
}
