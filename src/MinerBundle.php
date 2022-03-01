<?php

namespace DataMiner;

use DataMiner\DependencyInjection\MinerExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class MinerBundle extends Bundle
{
    public function getContainerExtension()
    {
        return new MinerExtension();
    }
}
