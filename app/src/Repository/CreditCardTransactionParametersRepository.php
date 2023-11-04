<?php

namespace App\Repository;

use App\Entity\CreditCardTransactionParameters;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CreditCardTransactionParameters>
 *
 * @method CreditCardTransactionParameters|null find($id, $lockMode = null, $lockVersion = null)
 * @method CreditCardTransactionParameters|null findOneBy(array $criteria, array $orderBy = null)
 * @method CreditCardTransactionParameters[]    findAll()
 * @method CreditCardTransactionParameters[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CreditCardTransactionParametersRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CreditCardTransactionParameters::class);
    }

//    /**
//     * @return CreditCardTransactionParameters[] Returns an array of CreditCardTransactionParameters objects
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

//    public function findOneBySomeField($value): ?CreditCardTransactionParameters
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
