<?php

namespace DataMiner\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class PropertyPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $registerService = $container->getDefinition('data_miner.property.registry');

        $properties = $container->findTaggedServiceIds('data_miner.property');

        foreach ($properties as $id => $attributes) {
            $registerService->addMethodCall('addType', [
                new Reference($id),
            ]);
        }
    }

}
