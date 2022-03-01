<?php

namespace PhpDataMiner\Model;

use ATV\BaseBundle\Entity\PInvoice;
use PhpDataMiner\Storage\Model\Discriminator\Discriminator;
use PhpDataMiner\Storage\Model\Discriminator\DiscriminatorInterface;
use PhpDataMiner\Storage\Model\Model as Base;
use PhpDataMiner\Storage\Model\ModelInterface;

/**
 * Description of Model
 *
 * @author Andres Pajo
 */
class Model extends Base implements ModelInterface
{
    /**
     * @param PInvoice $entity
     * @return DiscriminatorInterface
     */
    public static function createEntryDiscriminator($entity): DiscriminatorInterface
    {
        return new Discriminator([
            $entity->getCarrier() ? $entity->getCarrier()->getId() : null,
            $entity->getId(),
        ]);
    }
}
