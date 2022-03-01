<?php

namespace PhpDataMiner\Miner;

use PhpDataMiner\Miner as Base;
use PhpDataMiner\Model\Property\Provider;

class Miner extends Base
{
    /**
     * @var Provider
     */
    protected $provider;

    public function setProvider (Provider $provider)
    {
        $this->provider = $provider;
    }
}
