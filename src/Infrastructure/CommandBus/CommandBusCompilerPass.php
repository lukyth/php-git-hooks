<?php

namespace Infrastructure\CommandBus;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class CommandBusCompilerPass implements CompilerPassInterface
{
    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('command.bus')) {
            return;
        }

        $commandBus = $container->findDefinition('command.bus');
        $commandHandlers = $container->findTaggedServiceIds('command_handler');

        foreach ($commandHandlers as $id => $tags) {
            foreach ($tags as $attributes) {
                $commandBus->addMethodCall('addCommandHandler', [$attributes['handles'], $id]);
            }
        }
    }
}