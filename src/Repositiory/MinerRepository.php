<?php

namespace DataMiner\Repository;

use Doctrine\ORM\EntityRepository;
use PhpDataMinerStorage\StorageInterface;

class MinerRepository extends EntityRepository implements StorageInterface
{

}
