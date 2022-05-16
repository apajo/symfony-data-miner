<?php

namespace DataMiner\Model;

use PhpDataMiner\Storage\Model\FeatureInterface;
use PhpDataMiner\Storage\Model\LabelInterface;
use PhpDataMiner\Storage\Model\Property as Base;
use PhpDataMiner\Storage\Model\PropertyInterface;

/**
 * Description of Property
 *
 * @author Andres Pajo
 */
class Property extends Base implements PropertyInterface
{
    public static function createLabel (): LabelInterface
    {
        return new Label();
    }

    public static function createFeature (): FeatureInterface
    {
        return new Feature();
    }
}
