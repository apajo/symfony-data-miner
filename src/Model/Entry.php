<?php

namespace DataMiner\Model;

use PhpDataMiner\Storage\Model\Entry as Base;
use PhpDataMiner\Storage\Model\EntryInterface;
use DataMiner\Model\Property;
use PhpDataMiner\Storage\Model\PropertyInterface;

/**
 * Description of Entry
 *
 * @author Andres Pajo
 */
class Entry extends Base implements EntryInterface
{
    public static function createProperty(): PropertyInterface
    {
        return new Property();
    }
}
