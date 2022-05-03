<?php

namespace DataMiner\Model;

use ATV\BaseBundle\Entity\PInvoice;
use PhpDataMiner\Storage\Model\Discriminator\Discriminator;
use PhpDataMiner\Storage\Model\Discriminator\DiscriminatorInterface;
use PhpDataMiner\Storage\Model\EntryInterface;
use PhpDataMiner\Storage\Model\LabelInterface;
use PhpDataMiner\Storage\Model\Model as Base;
use PhpDataMiner\Storage\Model\ModelInterface;
use PhpDataMiner\Storage\Model\ModelPropertyInterface;

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

    public static function createProperty (): ModelPropertyInterface
    {
        return new ModelProperty();
    }

    public static function createEntry (): EntryInterface
    {
        return new Entry();
    }

    public static function createLabel (): LabelInterface
    {
        return new Label();
    }
}
