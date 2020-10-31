<?php

namespace App\Command;

use App\Entity\MO;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class PopulateDatabaseCommand extends Command
{
    protected static $defaultName = 'app:populate-database';
    private EntityManagerInterface $entityManager;
    private const MAX_ROWS = 7777;


    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        parent::__construct();
    }


    protected function configure()
    {
        $this->setDescription('Populate mo table');
    }


    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $entityManager = $this->entityManager;
        $connection = $entityManager->getConnection();
        $io = new SymfonyStyle($input, $output);

        $count = $connection->executeQuery('SELECT COUNT(*) FROM mo')->fetchOne();
        if($count > 0) {
            $io->warning('DB already populated.');

            return 0;
        }

        $io->note('Populating DB.');

        $texts = ['test data', 'on game', 'game news', 'stop game', 'stop all', 'on forum', 'stop forum'];
        $currentYear = (int) date('Y');
        for ($i = 0; $i < self::MAX_ROWS; $i++) {
            $year = mt_rand($currentYear - 5, $currentYear);
            $month = mt_rand(1, 12);
            $day = mt_rand(1, 31);

            $createdAt = \DateTimeImmutable::createFromFormat('Y-m-d', "$year-$month-$day");
            if ($createdAt === false) {
                --$i;
                continue;
            }

            $mo = new MO();
            $mo->setMsisdn(1111111);
            $mo->setOperatorid(1);
            $mo->setText($texts[array_rand($texts)]);
            $mo->setAuthToken('token');
            $mo->setNode('192.168.11.74');
            $mo->setCreatedAt($createdAt);
            $entityManager->persist($mo);
        }
        $entityManager->flush();
        $entityManager->clear();

        $io->success('DB was successfully populated.');

        return 0;
    }
}