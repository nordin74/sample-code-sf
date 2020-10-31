<?php

namespace App\Service\Processor;

use App\Repository\MORepository;
use App\Service\MOServiceResponse;
use App\Service\RegisterInterface;

final class DBProcessor implements ProcessorInterface
{
    private      $moRepository;
    private      $registrationSrv;
    private \PDO $pdo;


    public function __construct(\PDO $pdo, RegisterInterface $registrationSrv, MORepository $moRepository)
    {
        $this->moRepository = $moRepository;
        $this->registrationSrv = $registrationSrv;
        $this->pdo = $pdo;
    }


    /**
     * Write native queries because of performance
     */
    public function __invoke(int $limit): int
    {
        $requests = $this->moRepository->getLastUnprocessedRequestsIndexedById($limit);

        $callbacks = [];
        foreach ($requests as $id => $request) {
            unset($request['created_at']);
            $callbacks[$id] = $this->registrationSrv->register($request, true);
        }

        $queries = '';
        foreach ($callbacks as $id => $callback) {
            /** @var MOServiceResponse $MOServiceResponse */
            $MOServiceResponse = $callback();
            if(!$MOServiceResponse->getStatus()) {
                $row = (object) $requests[$id];
                $createdAt = $row->created_at->format('Y-m-d H:i:s');
                $queries .= 'INSERT INTO mofailed (msisdn, operatorid, node, `text`, created_at) ' .
                    "VALUES($row->msisdn, $row->operatorid, '$row->node', '$row->text', '$createdAt');\n";
                $queries .= "DELETE FROM mo WHERE id = $id;\n";
            } else {
                $authToken = $MOServiceResponse->getAuthToken();
                $queries .= "UPDATE mo SET auth_token = '" . $authToken . "' WHERE id = $id;\n";
            }
        }

        if(!empty($queries)) {
            $this->pdo->exec($queries);

            return count($requests);
        }

        return 0;
    }
}