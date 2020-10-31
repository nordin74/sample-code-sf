<?php
namespace App\Tests\Service\Processor;

use App\Entity\MO;
use App\Service\MOServiceTesting;
use App\Service\Processor\DBProcessor;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RegistrationDBProcessTest extends WebTestCase
{
    public static function setUpBeforeClass()
    {
        static::bootKernel();
    }


    public function testDBProcessorSuccess()
    {
        /** @var EntityManager $entityManager */
        $entityManager = static::$container->get('doctrine.orm.entity_manager');
        /** @var \PDO $pdo */
        $pdo = $entityManager->getConnection()->getWrappedConnection();
        $moRepository = $entityManager->getRepository(MO::class);


        $this->loadSubscriptionsFixtures();
        $registration = new DBProcessor($pdo, new MOServiceTesting(), $moRepository);

        $processedRequest = $registration(11);
        $this->assertEquals(2, $processedRequest);

        $failedRegistrations = $entityManager->createQuery('SELECT mof FROM App\Entity\MOFailed mof')
            ->getArrayResult();
        $this->assertCount(1, $failedRegistrations);

        $failedRegistration = reset($failedRegistrations);
        self::assertEquals('ABRACADABRA', $failedRegistration['text']);
    }


    private function loadSubscriptionsFixtures()
    {
        /** @var EntityManager $entityManager */
        $entityManager = static::$container->get('doctrine.orm.entity_manager');
        $entityManager->getConnection()->getConfiguration()->setSQLLogger(null);

        $columns = ['msisdn', 'operatorid', 'node','created_at','text', 'auth_token'];
        $values = [
            [123456, 5, '172.25.0.1', '2020-06-09 13:22:10', 'ON TEST', null],
            [111111, 10, '172.25.0.2', '2020-06-09 13:22:11', 'ABRACADABRA', null],
            // Request already processed:
            [222222, 7, '172.25.0.3', '2020-06-08 13:22:12', 'ON EÑE', '123456789ABCDEFGHIJKLMNOPQ']
        ];

        for ($i = 0; $i < count($values); $i++) {
            $row = array_combine($columns, $values[$i]);
            $mo = new MO();
            $mo->setMsisdn($row['msisdn']);
            $mo->setOperatorid($row['operatorid']);
            $mo->setText($row['text']);
            $mo->setAuthToken($row['auth_token']);
            $mo->setNode($row['node']);
            $mo->setCreatedAt(new \DateTimeImmutable($row['created_at']));
            $entityManager->persist($mo);
        }
        $entityManager->flush();
        $entityManager->clear();
    }


    public function tearDown()
    {
        $entityManager = static::$container->get('doctrine.orm.entity_manager');
        $connection = $entityManager->getConnection();
        $connection->executeStatement($connection->getDatabasePlatform()->getTruncateTableSQL('mo'));
        $connection->executeStatement($connection->getDatabasePlatform()->getTruncateTableSQL('mofailed'));
    }
}