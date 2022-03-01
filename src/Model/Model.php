<?php

namespace DataMinerBundle\Model;

use ATV\BaseBundle\Entity\PInvoice;
use DataMiner\Storage\Model\Discriminator\Discriminator;
use DataMiner\Storage\Model\Discriminator\DiscriminatorInterface;
use DataMiner\Storage\Model\EntryInterface;
use DataMiner\Storage\Model\Model as Base;
use DataMiner\Storage\Model\ModelInterface;

/**
 * Description of Model
 *
 * @author Andres Pajo
 */
class Model extends Base implements ModelInterface
{

    public static function createEntry(): EntryInterface
    {
        return new StorageEntry();
    }

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
