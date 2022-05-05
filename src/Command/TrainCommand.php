<?php

namespace DataMiner\Command;

use ATV\BaseBundle\Client\Entity\AbstractClient;
use Core\BaseBundle\Media\MediaManagerTrait;
use Core\BaseBundle\Traits\EntityManagerAwareTrait;
use DataMiner\Command\Data\DataItem;
use DataMiner\Command\Data\DocumentIterator;
use DataMiner\Command\Data\ProviderInterface;
use DataMiner\Miner\Miner;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class TrainCommand extends Command
{
    use EntityManagerAwareTrait;
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

    /**
     * @required
     * @param Miner $miner
     */
    public function setMiner (Miner $miner)
    {
        $this->miner = $miner;
    }

    /**
     * @var ProviderInterface|null
     */
    protected ?ProviderInterface $data = null;

    /**
     * @param ProviderInterface $data
     */
    public function setDataProvider (ProviderInterface $data)
    {
        $this->data = $data;
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

        $clients = $this->data->getIterator([
            'invoices' => $invoiceIds,
            'clients' => $clientIds,
        ]);

        /**
         * @var  $key
         * @var DocumentIterator $documents
         */
        foreach ($clients as $key => $documents) {
            $this->process($documents->getClient(), $documents);
        }

    }

    protected function process (AbstractClient $client, DocumentIterator $items)
    {
        $this->io->newLine();
        $this->io->title($client->getName());

        $progressBar = new ProgressBar($this->output, $items->count());
        $progressBar->setRedrawFrequency(2);

        $progressBar->setFormat('debug');
        //$progressBar->setFormat(' %current%/%max% [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s% %memory:6s%');
        $progressBar->start();

        /** @var DataItem $item */
        foreach ($items as $key => $item) {
            $progressBar->advance();

            if (!$item || !$item->getContent()) {
                continue;
            }

            $content = $item->getContent();

            $miner = $this->miner->create($item);

            $doc = $miner->normalize($content);
            $entry = $miner->train($item, $doc);

            $this->em->persist($entry);
        }

        $this->em->flush();
        $progressBar->finish();
        $this->io->newLine();

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
