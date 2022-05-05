<?php

namespace DataMiner\Command;

use ATV\BaseBundle\Client\Entity\AbstractClient;
use ATV\BaseBundle\Entity\PInvoice;
use Core\BaseBundle\Media\MediaManagerTrait;
use DataMiner\Miner\Miner;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class TrainCommand extends Command
{
    use MediaManagerTrait;

    /** @var InputInterface */
    protected $input;

    /** @var OutputInterface */
    protected $output;

    /**
     * @var SymfonyStyle
     */
    protected $io;

    /**
     * @var Miner
     */
    protected $miner;

    /** @var EntityManagerInterface */
    protected $em;

    /**
     * @var string
     */
    protected $model;

    /**
     * @required
     * @param Miner $miner
     */
    public function setMiner (Miner $miner)
    {
        $this->miner = $miner;
    }

    /**
     * @required
     * @param EntityManagerInterface $entityManager
     * @return $this
     */
    public function setEntityManager(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
        return $this;
    }

    public function setModal(string $model)
    {
        $this->model = $model;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('miner:train')
            ->setDescription('Train your model')
//            ->addArgument('model', InputArgument::REQUIRED, 'Model name')
//            ->addOption('discriminator', 'd', InputOption::VALUE_OPTIONAL, 'Train only discriminated model entries')
            ->addOption('override', 'o', InputOption::VALUE_OPTIONAL, 'Override already trained entries', false)
            ->addOption('invoice', 'i', InputOption::VALUE_OPTIONAL, 'Comma separated invoice document numbers', false)
            ->addOption('client', 'c', InputOption::VALUE_OPTIONAL, 'Comma separated client IDs', false)
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;
        $this->io = new SymfonyStyle($input, $output );

        $clientIds = $this->getOption('client', true);
        $invoiceIds = $this->getOption('invoice', true);

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

        /** @var AbstractClient $client */
        foreach ($clients as $client) {
            $repo = $this->getRepository($this->model);
            $qb = $this->createQueryBuilder($repo);

            $whereX = [
                $qb->expr()->eq('main.carrier', $client->getId())
            ];

            $invoiceIds && $whereX[] = $qb->expr()->in('main.id', $invoiceIds);

            $qb->andWhere($qb->expr()->andX(...$whereX));

            $items = $qb->getQuery()->getResult();
            $this->process($client, new ArrayCollection($items));
        }

    }

    protected function process (AbstractClient $client, Collection $items)
    {
        $this->io->title($client->getName());

        $progressBar = new ProgressBar($this->output, $items->count());
        $progressBar->setRedrawFrequency(2);

        $progressBar->setFormat('debug');
        //$progressBar->setFormat(' %current%/%max% [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s% %memory:6s%');
        $progressBar->start();

        /** @var PInvoice $item */
        foreach ($items as $item) {
            if ($item->getFiles()->isEmpty()) {
                continue;
            }

            $miner = $this->miner->create($item);
            $filePath = $this->mediaManager->getMediaPath($item->getFiles()->first());

            if (!is_file($filePath)) {
                continue;
            }

            $content = shell_exec('pdftotext -layout ' . $filePath . ' -');

            if (!$content) {
                continue;
            }

            $doc = $miner->normalize($content);
            $entry = $miner->train($item, $doc);

            $progressBar->advance();
        }

        $this->em->flush();
        $progressBar->finish();

        ProgressBar::setFormatDefinition(
            'minimal',
            '<info>%percent%</info>\033[32m%\033[0m <fg=white;bg=blue>%remaining%</>'
        );

    }

    protected function getOption (string $name, bool $multiple = false)
    {
        if ($multiple) {
            $result = $this->input->getOption($name);
            $result = explode(',', $result);
            $result = array_filter(array_map('trim', $result));

            return $result;
        }

        return $this->input->getOption($name);
    }

    protected function getRepository (string $model)
    {
        return $this->em->getRepository($model);
    }

    protected function createQueryBuilder (EntityRepository $repository)
    {
        $qb = $repository->createQueryBuilder('main');


        return $qb;
    }
}
