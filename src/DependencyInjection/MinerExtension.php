<?php

namespace DataMiner\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

class MinerExtension extends Extension implements PrependExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container) {
        $this->loadConfig($configs, $container, 'services.yml');
    }

    protected function loadConfig(array $configs, ContainerBuilder $container, $name)
    {
        $location = realpath(__DIR__ . '/../Resources/config');
        $loader = new Loader\YamlFileLoader($container, new FileLocator($location));
        $loader->load($name);
    }


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
