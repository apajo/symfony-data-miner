<?php

namespace DataMinerBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

abstract class MinerExtension extends Extension implements PrependExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function prepend(ContainerBuilder $container)
    {
        $bundles = $container->getParameter('kernel.bundles');

        if (isset($bundles['DoctrineBundle'])) {
            $configs = $container->getExtensionConfig($this->getAlias());
            $this->loadConfig($configs, $container, 'doctrine.yml');
        }

    }

    public function getAlias()
    {
        return 'data_miner';
    }
}
