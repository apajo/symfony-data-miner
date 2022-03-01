<?php

namespace DataMinerBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class MinerBundle extends Bundle
{
    public function getContainerExtension()
    {
        return new MinerExtension();
    }
}
