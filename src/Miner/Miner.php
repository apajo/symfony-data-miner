<?php

namespace DataMiner\Miner;

use Core\BaseBundle\Traits\EntityManagerAwareTrait;
use DataMiner\Model\Model;
use PhpDataMiner\DataMiner;
use PhpDataMiner\Model\Property\Provider;
use PhpDataMiner\Storage\StorageInterface;

class Miner
{
    use EntityManagerAwareTrait;

    /**
     * @var Provider
     */
    protected $provider;

    protected $filters = [];

    protected $options = [];

    function __construct (Provider $provider, array $filters, $options = [])
    {
        $this->provider = $provider;
        $this->filters = $filters;
        $this->options = $options;
    }

    public function create ($entity)
    {
        return DataMiner::create(
            $entity,
            $this->provider,
            $this->em->getRepository(Model::class),
            $this->filters,
            $this->options
        );
    }
}
