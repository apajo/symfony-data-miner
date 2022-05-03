<?php


namespace DataMiner\Miner;

use PhpDataMiner\Kernel\AbstractKernel;
use PhpDataMiner\Kernel\KernelInterface;
use Rubix\ML\Classifiers\KNearestNeighbors;
use Rubix\ML\Kernels\Distance\Manhattan;

/**
 * Description of DefaultKernel
 *
 * @author Andres Pajo
 */
class DefaultKernel extends AbstractKernel implements KernelInterface
{
    function __construct ()
    {
        $this->kernel = new KNearestNeighbors(3, false, new Manhattan());
    }

}
