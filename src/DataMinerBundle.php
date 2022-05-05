<?php

namespace DataMiner;

use DataMiner\DependencyInjection\Compiler\PropertyPass;
use DataMiner\DependencyInjection\MinerExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class DataMinerBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new PropertyPass());
    }

    public function getContainerExtension()
    {
        return new MinerExtension();
    }
}
