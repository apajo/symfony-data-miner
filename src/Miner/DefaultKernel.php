<?php


namespace DataMiner\Miner;

use PhpDataMiner\Kernel\AbstractKernel;
use PhpDataMiner\Kernel\KernelInterface;
use PhpDataMiner\Storage\Model\EntryInterface;
use PhpDataMiner\Storage\Model\ModelInterface;
use PhpDataMiner\Model\Property\PropertyInterface;
use PhpDataMiner\Normalizer\Document\Document;
use PhpDataMiner\Normalizer\Document\Pointer;
use PhpDataMiner\Normalizer\Tokenizer\Token\TokenInterface;
use Rubix\ML\Classifiers\KNearestNeighbors;
use Rubix\ML\Datasets\Labeled;
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

    public function train (EntryInterface $entry, PropertyInterface $property)
    {
        $samples = $entry->getModel()->resolveSamples($property);

        $dataset = new Labeled(
            array_map(function ($a) {
                return array_map('intval', explode('.', $a));
            }, array_column($samples, 'data'))
            , array_column($samples, 'label')
        );

        // $this->kernel->train($dataset);
    }

    public function predict (ModelInterface $model, PropertyInterface $property, Document $doc): ?TokenInterface
    {
        $labels = $this->groupByValues($model, $property);

        foreach ($labels as $index => $entries ) {
            $result = $doc->getTraverser()->getValue(
                new Pointer(explode('.', $index)),
                $property
            );

            if (!$result) {
                continue;
            }

            $value = $property->normalize($result->getValue(), null);

            if (!$value) {
                continue;
            }

            return $result;
        }

        return null;
    }

    public function groupByValues (ModelInterface $model, PropertyInterface $target): array
    {
        $labels = [];

        foreach ($model->getEntries() as $entry) {
            $prop = $entry->getProperty($target->getPropertyPath());

            if (!$prop) {
                continue;
            }

            $value = $prop->getLabel()->getValue();

            if (!isset($labels[$value])){
                $labels[$value] = [];
            }

            $labels[$value][] = $entry->getId();
        }

        uksort($labels, function (string $a, string $b) use ($labels) {
            return count($labels[$b]) - count($labels[$a]);
        });

        return $labels;
    }
}
