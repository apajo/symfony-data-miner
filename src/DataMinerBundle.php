<?php

namespace DataMiner;

use DataMiner\DependencyInjection\MinerExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class DataMinerBundle extends Bundle
{
    public function getContainerExtension()
    {
        return new MinerExtension();
    }
}
