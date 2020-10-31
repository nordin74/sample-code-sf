<?php

namespace App\Repository;

use App\Entity\MO;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class MORepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MO::class);
    }


    public function countLastInsertions(int $minutes): int
    {
        $minutesAgo = date('Y-m-d H:i:s', strtotime("$minutes minutes ago"));

        $query = $this->createQueryBuilder('m');
        return $query->select('COUNT(1)')
            ->where('m.created_at > :minutesAgo')
            ->setParameter('minutesAgo', $minutesAgo)
            ->getQuery()
            ->getSingleScalarResult();
    }


    public function getTimeSpan(int $limit): array
    {
        $query = $this->createQueryBuilder('m');

        return $query->select('MIN(m.created_at) AS min, MAX(m.created_at) AS max')
            ->orderBy('m.id', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getSingleResult();
    }


    public function getLastUnprocessedRequestsIndexedById($limit) : array
    {
        $query = $this->createQueryBuilder('m');
        $rows = $query->select()
            ->where('m.auth_token IS NULL')
            ->setMaxResults($limit)
            ->getQuery()
            ->getArrayResult();

        $indexed = [];
        foreach($rows as $row) {
            $id = $row['id'];
            unset($row['id']);
            $indexed[$id] = $row;
        }

        return $indexed;
    }


    public function getTotalUnprocessedRequests(): int
    {
        $query = $this->createQueryBuilder('m');
        $count = $query->select('COUNT(1)')
            ->where('m.auth_token IS NULL')
            ->getQuery()
            ->getSingleScalarResult();

        return $count;
    }


    public function deleteUnprocessedRequests(): int
    {
        $query = $this->createQueryBuilder('m');
        $deletedRows = $query->delete()
            ->where('m.auth_token IS NULL')
            ->getQuery()
            ->execute();

        return $deletedRows;
    }
}