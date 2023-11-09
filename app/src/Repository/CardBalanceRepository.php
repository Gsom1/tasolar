<?php

namespace App\Repository;

use App\Entity\CardBalance;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CardBalance>
 *
 * @method CardBalance|null find($id, $lockMode = null, $lockVersion = null)
 * @method CardBalance|null findOneBy(array $criteria, array $orderBy = null)
 * @method CardBalance[]    findAll()
 * @method CardBalance[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CardBalanceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CardBalance::class);
    }

//    /**
//     * @return CardBalance[] Returns an array of CardBalance objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?CardBalance
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
