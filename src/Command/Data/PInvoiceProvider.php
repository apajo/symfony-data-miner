<?php

namespace DataMiner\Command\Data;

use ArrayIterator;
use ATV\BaseBundle\Client\Entity\AbstractClient;
use ATV\BaseBundle\Entity\PInvoice;
use Core\BaseBundle\Media\MediaManagerTrait;
use Core\BaseBundle\Traits\EntityManagerAwareTrait;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Iterator;

class PInvoiceProvider extends AbstractProvider
{
    use EntityManagerAwareTrait;
    use MediaManagerTrait;

    public function getIterator (array $options = []): Iterator
    {
        $this->buildOptions($options);

        $clientIds = $this->getOption('clients');
        $invoiceIds = $this->getOption('invoices');

        $repo = $this->getRepository(AbstractClient::class);
        $qb = $this->createQueryBuilder($repo);

        $whereX = [];

        if ($clientIds) {
            $whereX[] = $qb->expr()->in('main.id', $clientIds);
        }

        if ($whereX) {
            $qb->andWhere($qb->expr()->andX(...$whereX));
        }

        $clients = $qb->getQuery()->getResult();

        $result = [];

        /** @var AbstractClient $client */
        foreach ($clients as $client) {
            $repo = $this->getRepository(PInvoice::class);
            $qb = $this->createQueryBuilder($repo);
            $qb->leftJoin('main.files', 'files');
            $qb->leftJoin('main.carrier', 'carrier');

            $whereX = [
                $qb->expr()->in('carrier.id', $client->getId()),
                $qb->expr()->isNotNull('files')
            ];

            $invoiceIds && $whereX[] = $qb->expr()->in('main.id', $invoiceIds);

            $qb->andWhere($qb->expr()->andX(...$whereX));

            $items = $qb->getQuery()->getResult();

            $docIter = new DocumentIterator(
                new ArrayIterator($items)
            );
            $docIter->setMediaManager($this->mediaManager);
            $docIter->setClient($client);

            $result[] = $docIter;
        }

        return new ClientIterator(new ArrayIterator($result));
    }

    protected function getRepository ($model)
    {
        return $this->em->getRepository($model);
    }

    protected function createQueryBuilder (EntityRepository $repository)
    {
        $qb = $repository->createQueryBuilder('main');
        return $qb;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'invoices' => [],
            'clients' => [],
        ));
    }
}
