<?php

namespace DataMiner\Model;

use PhpDataMiner\Storage\Model\LabelInterface;
use PhpDataMiner\Storage\Model\ModelProperty;
use PhpDataMiner\Storage\Model\Property as Base;
use PhpDataMiner\Storage\Model\PropertyInterface;

/**
 * Description of Property
 *
 * @author Andres Pajo
 */
class Property extends Base implements PropertyInterface
{
    /**
     * @var ModelProperty|null
     */
    protected ?ModelProperty $model_property = null;


    public function getModelProperty(): ?ModelProperty
    {
        return $this->model_property;
    }

    public function setModelProperty(?ModelProperty $model_property): void
    {
        $this->model_property = $model_property;
    }


    public static function createLabel (): LabelInterface
    {
        return new Label();
    }
}
