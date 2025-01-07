<?php

namespace App\Repository;

use App\Entity\Immobilier;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Immobilier>
 */
class ImmobilierRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Immobilier::class);
    }
// src/Repository/ImmobilierRepository.php
    public function findBySearchCriteria(string $region, float $minPrix, float $maxPrix): array
    {
        $qb = $this->createQueryBuilder('i');

        if (!empty($region)) {
            $qb->andWhere('i.region LIKE :region')
                ->setParameter('region', '%' . $region . '%');
        }

        $qb->andWhere('i.prix BETWEEN :minPrix AND :maxPrix')
            ->setParameter('minPrix', $minPrix)
            ->setParameter('maxPrix', $maxPrix);

        return $qb->getQuery()->getResult();
    }

    //    /**
    //     * @return Immobilier[] Returns an array of Immobilier objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('i')
    //            ->andWhere('i.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('i.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Immobilier
    //    {
    //        return $this->createQueryBuilder('i')
    //            ->andWhere('i.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
