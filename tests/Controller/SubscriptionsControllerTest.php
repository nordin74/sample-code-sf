<?php

namespace App\Tests\Controller;

use App\Entity\MO;
use App\Tests\_helpers\ClientHelper;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SubscriptionsControllerTest extends WebTestCase
{
    use ClientHelper;

    public function testSubscribeSuccessful()
    {
        $payload = ['msisdn' => 742788, 'operatorid' => 3, 'text' => 'ON LEARNING'];
        $client = $this->createClient();
        $this->jsonRequest($client, 'POST', '/subscribe', $payload);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $content = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals(['status' => 'ok'], $content);
    }


    public function testSubscribeFailWrongPayloads()
    {
        $operatorIdNotAllowed = 11;
        $payload = ['msisdn' => 742788, 'operatorid' => $operatorIdNotAllowed, 'text' => 'ON LEARNING'];
        $client = $this->createClient();
        $this->jsonRequest($client, 'POST', '/subscribe', $payload);
        $this->assertEquals(400, $client->getResponse()->getStatusCode());

        $content = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals(
            ['title' => 'Bad Request', 'status' => 400, 'detail' => 'Invalid parameter: operatorid'],
            $content
        );
    }


    public function testStatisticsSuccessful()
    {
        $this->loadStatsFixtures();

        $client = static::$container->get('test.client');
        $this->jsonRequest($client, 'GET', '/stats');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $content = json_decode($client->getResponse()->getContent(), true);

        $this->assertEquals(15, $content['last_mo_inserts_mins']);

        $dtMax = new \DateTimeImmutable($content['time_span_last_10k']['max']);
        $dtMin = new \DateTimeImmutable($content['time_span_last_10k']['min']);
        $diffInSeconds = ($dtMax->getTimestamp() - $dtMin->getTimestamp()) -
            ($dtMin->getOffset() - $dtMax->getOffset());

        // 600000 secs => 10000 mins
        $this->assertEquals(600000, $diffInSeconds);
    }


    private function loadStatsFixtures()
    {
        $entityManager = static::bootKernel()->getContainer()->get('doctrine.orm.entity_manager');
        $entityManager->getConnection()->getConfiguration()->setSQLLogger(null);

        $timeNow = time();
        for ($i = 0; $i < 10001; $i++) {
            $createdAt = date('Y-m-d H:i:s', strtotime("now - $i min", $timeNow));
            $mo = new MO();
            $mo->setMsisdn(123456);
            $mo->setOperatorid(7);
            $mo->setText('ON GAMES');
            $mo->setAuthToken('123456789ABCDEFGHIJKLMNOPQ');
            $mo->setNode('172.25.0.1');
            $mo->setCreatedAt(new \DateTimeImmutable($createdAt));
            $entityManager->persist($mo);
        }
        $entityManager->flush();
        $entityManager->clear();
    }


    public static function tearDownAfterClass()
    {
        $entityManager = static::bootKernel()->getContainer()->get('doctrine.orm.entity_manager');
        $connection = $entityManager->getConnection();
        $connection->executeStatement($connection->getDatabasePlatform()->getTruncateTableSQL('mo'));
    }
}
