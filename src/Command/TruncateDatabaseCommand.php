<?php

namespace App\Command;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class TruncateDatabaseCommand extends Command
{
    protected static $defaultName = 'app:truncate-database';
    private EntityManagerInterface $entityManager;


    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        parent::__construct();
    }


    protected function configure()
    {
        $this->setDescription('Clear mo, mofailed & doctrine_migration tables');
        $this->addOption('migrations', null, InputOption::VALUE_NONE, 'Truncate migration table');
    }


    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $entityManager = $this->entityManager;

        $connection = $entityManager->getConnection();
        $connection->executeStatement($connection->getDatabasePlatform()->getTruncateTableSQL('mo'));
        $connection->executeStatement($connection->getDatabasePlatform()->getTruncateTableSQL('mofailed'));

        if ($input->getOption('migrations') !== false) {
            $connection->executeStatement(
                $connection->getDatabasePlatform()->getTruncateTableSQL('doctrine_migration_versions')
            );
        }

        $io = new SymfonyStyle($input, $output);
        $io->success('DB was successfully truncated.');

        return 0;
    }
}