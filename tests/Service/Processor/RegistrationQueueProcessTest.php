<?php

namespace App\Tests\Service\Processor;

use App\Service\MOServiceTesting;
use App\Service\Processor\QueueProcessor;
use App\Service\Queue\RedisQueue;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RegistrationQueueProcessTest extends WebTestCase
{
    public static function setUpBeforeClass()
    {
        static::bootKernel();
    }


    public function testArrayQueueSuccess()
    {
        $queue = static::$container->get('queue_interface');
        $entityManager = static::$container->get('doctrine.orm.entity_manager');
        /** @var \PDO $pdo */
        $pdo = $entityManager->getConnection()->getNativeConnection();


        $requests = $this->getRequests();
        foreach ($requests as $request) {
            $queue->push(json_encode($request));
        }
        $registration = new QueueProcessor($pdo, new MOServiceTesting(), $queue);

        $processedRequest = $registration(11);
        $this->assertEquals($processedRequest, 3);

        $failedRegistrations = $entityManager->createQuery('SELECT mof FROM App\Entity\MOFailed mof')
            ->getArrayResult();
        $this->assertCount(1, $failedRegistrations);

        $failedRegistration = reset($failedRegistrations);
        $this->assertEquals('ABRACADABRA', $failedRegistration['text']);
    }


    public function testRedisQueueSuccess()
    {
        $entityManager = static::$container->get('doctrine.orm.entity_manager');
        /** @var \PDO $pdo */
        $pdo = $entityManager->getConnection()->getNativeConnection();


        $requests = $this->getRequests();

        $mockRedisQueue = $this->createMock(RedisQueue::class);
        $mockRedisQueue->expects($this->once())->method('clearBackUp');
        $mockRedisQueue->expects($this->exactly(4))
            ->method('pullAndBackUp')
            ->willReturnOnConsecutiveCalls(
                json_encode(array_shift($requests)),
                json_encode(array_shift($requests)),
                json_encode(array_shift($requests)),
            );
        $registration = new QueueProcessor($pdo, new MOServiceTesting(), $mockRedisQueue);

        $processedRequest = $registration(11);
        self::assertEquals($processedRequest, 3);

        $failedRegistrations =  $entityManager->createQuery('SELECT mof FROM App\Entity\MOFailed mof')
            ->getArrayResult();
        self::assertCount(1, $failedRegistrations);

        $failedRegistration = reset($failedRegistrations);
        self::assertEquals('ABRACADABRA', $failedRegistration['text']);
    }


    private function getRequests()
    {
        return [
            [
                'msisdn'      => 123456,
                'operatorid'  => 5,
                'created_at'  => '2020-06-09 13:22:10',
                'text'        => 'ON TEST',
                'node'        => '172.25.0.1'
            ],
            [
                'msisdn'      => 111111,
                'operatorid'  => 1,
                'created_at'  => '2020-06-09 13:22:11',
                'text'        => 'ABRACADABRA',
                'node'        => '172.25.0.2'
            ],
            [
                'msisdn'      => 222222,
                'operatorid'  => 7,
                'created_at'  => '2020-06-08 13:22:12',
                'text'        => 'ON EÑE',
                'node'        => '172.25.0.3'
            ]
        ];
    }


    public function tearDown()
    {
        $entityManager = static::$container->get('doctrine.orm.entity_manager');
        $connection = $entityManager->getConnection();
        $connection->executeStatement($connection->getDatabasePlatform()->getTruncateTableSQL('mo'));
        $connection->executeStatement($connection->getDatabasePlatform()->getTruncateTableSQL('mofailed'));
    }
}