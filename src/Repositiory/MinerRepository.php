<?php

namespace DataMiner\Repository;

use Doctrine\ORM\EntityRepository;
use PhpDataMiner\Storage\StorageInterface;

class MinerRepository extends EntityRepository implements StorageInterface
{

}
