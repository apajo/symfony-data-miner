<?php

namespace DataMiner\Miner;

use Core\BaseBundle\Entity\Repository\EntityRepository;
use DataMiner\Model\Entry;
use DataMiner\Model\Model;
use PhpDataMiner\Storage\Model\EntryInterface;
use PhpDataMiner\Storage\Model\ModelInterface;
use PhpDataMiner\Storage\StorageInterface;
use PhpDataMiner\Storage\StorageTrait;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Storage extends EntityRepository implements StorageInterface
{
    use StorageTrait;

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


    public function filterEntries(ModelInterface $model, array $filter = null)
    {
        $model->filterEntries(function (EntryInterface $a) use ($filter) {
            $regex = '/' . implode('\.', array_map(function ($a) {
                    return '(' . ($a ?: '\d*') . ')';
                }, $filter)) . '/';
            $target = $a->getDiscriminator()->getString();

            preg_match($regex, $target, $matches);

            return (bool)$matches;
        });
    }

    protected function buildOptions(array $options = [])
    {
        $resolver = new OptionsResolver();
        $this->configureOptions($resolver);
        $this->options = $resolver->resolve($options);
    }

    protected function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'discriminator' => null,
            'property' => null,
        ));
    }
}
