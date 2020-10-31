<?php

namespace App\Command;

use App\Entity\MO;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class DeleteUnprocessedCommand extends Command
{
    protected static $defaultName = 'app:del-unprocessed-reqs';
    private EntityManagerInterface $entityManager;


    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        parent::__construct();
    }


    protected function configure()
    {
        $this->setDescription('Delete unprocessed requests');
    }


    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $entityManager = $this->entityManager;

        // TODO: WIP

        $deletedRows = rand(1, 11111);
        $io = new SymfonyStyle($input, $output);
        $io->success("Deleted $deletedRows unprocessed requests.");

        return 0;
    }
}