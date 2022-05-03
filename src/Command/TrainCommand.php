<?php

namespace DataMiner\Command;

use ATV\BaseBundle\Entity\PInvoice;
use DataMiner\Miner\Miner;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TrainCommand extends Command
{
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
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('miner:train')
            ->setDescription('Train your model')
//            ->addArgument('model', InputArgument::REQUIRED, 'Model name')
//            ->addOption('discriminator', 'd', InputOption::VALUE_OPTIONAL, 'Train only discriminated model entries')
//            ->addOption('override', 'o', InputOption::VALUE_OPTIONAL, 'Override already trained entries', false)
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $entity = new PInvoice();
        dump($this->miner->create($entity));
    }
}
