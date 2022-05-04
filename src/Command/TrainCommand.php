<?php

namespace DataMiner\Command;

use ATV\BaseBundle\Entity\PInvoice;
use Core\BaseBundle\Traits\EntityManagerAwareTrait;
use DataMiner\Miner\Miner;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Helper\ProgressBar;
use function _HumbugBox113887eee2b6\iter\rewindable\repeat;

class TrainCommand extends Command
{
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

        $clients = $this->getOption('client', true);

        $repo = $this->getRepository();
        $qb = $this->createQueryBuilder($repo);
        $qb->andWhere($qb->expr()->andX(...[
            $qb->expr()->in('main.client', $clients)
        ]));
        dump($qb->getDQL());
dump($qb->getQuery()->getResult());




        $this->process();
    }

    protected function process (Collection $items)
    {
        $progressBar = new ProgressBar($this->output, 10000);
        $progressBar->setRedrawFrequency(2);

        $progressBar->setFormat('debug');
        //$progressBar->setFormat(' %current%/%max% [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s% %memory:6s%');
        $progressBar->start();

        do {
            $progressBar->advance();
        } while ($progressBar->getProgress() < $progressBar->getMaxSteps());

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

    protected function getRepository ()
    {
        return $this->em->getRepository($this->model);
    }

    protected function createQueryBuilder (EntityRepository $repository)
    {
        $qb = $repository->createQueryBuilder('main');


        return $qb;
    }
}
