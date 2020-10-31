<?php

namespace App\Service\Processor;

use App\Service\Queue\QueueInterface;
use App\Service\MOServiceResponse;
use App\Service\RegisterInterface;

final class QueueProcessor
{
    private \PDO $pdo;
    private RegisterInterface $registrationSrv;
    private QueueInterface $queue;


    public function __construct(\PDO $pdo, RegisterInterface $registrationSrv, QueueInterface $queue)
    {
        $this->pdo = $pdo;
        $this->registrationSrv = $registrationSrv;
        $this->queue = $queue;
    }


    /**
     * Write native queries because of performance
     */
    public function __invoke(int $limit): int
    {
        $counter = 0;
        $callbacks = $requests = [];
        while ($limit >= ++$counter && ($message = $this->queue->pullAndBackUp())) {
            $request = json_decode($message, true);
            $requests[$counter] = $request;
            unset($request['created_at']);
            $callbacks[$counter] = $this->registrationSrv->register($request, true);
        }

        $queries = '';
        foreach ($callbacks as $id => $callback) {
            /** @var MOServiceResponse $MOServiceResponse */
            $MOServiceResponse = $callback();
            $row = (object) $requests[$id];
            if(!$MOServiceResponse->getStatus()) {
                $queries .= 'INSERT INTO mofailed (msisdn, operatorid, node, `text`, created_at) ' .
                    "VALUES($row->msisdn, $row->operatorid, '$row->node', '$row->text', '$row->created_at');\n";
            } else {
                $authToken = $MOServiceResponse->getAuthToken();
                $queries .= 'INSERT INTO mo (msisdn, operatorid, node, `text`, created_at, auth_token) ' .
                    "VALUES($row->msisdn, $row->operatorid, '$row->node', '$row->text', '$row->created_at', '$authToken');\n";
            }
        }

        if(!empty($queries)) {
            if ($this->pdo->exec($queries) > 0) {
                $this->queue->clearBackUp();
            }

            return count($requests);
        }

        return 0;
    }
}