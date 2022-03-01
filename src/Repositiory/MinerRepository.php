<?php

namespace PhpDataMiner\Repository;

use PhpDataMiner\Model\Entry;
use PhpDataMiner\Model\Model;
use Doctrine\ORM\EntityRepository;
use PhpDataMiner\Storage\StorageInterface;
use PhpDataMiner\Storage\StorageTrait;
use PhpDataMiner\Storage\Model\ModelInterface;

class MinerRepository extends EntityRepository implements StorageInterface
{
    use StorageTrait;

    protected $labelModel = Label::class;
    protected $entryModel = Entry::class;
    protected $propertyModel = Property::class;

    public function load($entity, array $options = []): ModelInterface
    {
        $this->buildOptions($options);

        $model = $this->getModel($entity);

        $discriminator = $model::createEntryDiscriminator($entity);
        $model->getEntries($discriminator);

        return $model;
    }

    /**
     * @param Entry $entry
     * @return bool
     */
    public function append($entry): bool
    {
        /** @var Model $item */
        $item = $this->findOneBy([
            'model' => $entry->getModel(),
            'discriminator' => $entry->getDiscriminator(),
            'name' => $entry->getId(),
        ]);

        if ($item) {
            $item->setModel($entry->getModel()->getModel());
            $item->setName($entry->getModel()->getModel());

            return true;
        }

        if (!$item) {
            $this->getEntityManager()->persist($entry);
        }

        $this->save($entry->getModel());

        return true;
    }

    public function save(ModelInterface $model): bool
    {
        $this->getEntityManager()->flush();

        return true;
    }

    public function getModel($entity, bool $create = true): ?ModelInterface
    {
        $model = $this->findOneBy([
            'model' => get_class($entity),
        ]);

        if (!$model && $create) {
            $model = new Model();
            $model->setModel(get_class($entity));

            $this->_em->persist($model);
        }

        return $model;
    }
}
