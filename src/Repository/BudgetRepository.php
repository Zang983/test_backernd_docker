<?php

namespace App\Repository;

use App\Entity\Budget;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Budget>
 */
class BudgetRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Budget::class);
    }

    public function findByUserWithoutTransactions(User $user): array
    {
        // On récupère les budgets avec les transactions associées des 30 derniers jours
        return $this->createQueryBuilder('b')
            ->andWhere('b.ownerUser = :userId')
            ->setParameter('userId', $user->getId())
            ->getQuery()
            ->getArrayResult();
    }

    public function findByUserWithTransactionsAndParties(User $user): array
    {
        $userId = $user->getId();
        $firstDayOfMonth = new \DateTime('first day of this month midnight');
        $lastDayOfMonth = new \DateTime('last day of this month 23:59:59');

        return $this->createQueryBuilder('b')
            ->leftJoin('b.transactions', 't')
            ->leftJoin('t.parties', 'p')
            ->addSelect('t', 'p')
            ->andWhere('b.ownerUser = :userId')
            ->andWhere('t.transectedAt BETWEEN :start AND :end')
            ->setParameter('userId', $userId)
            ->setParameter('start', $firstDayOfMonth)
            ->setParameter('end', $lastDayOfMonth)
            ->getQuery()
            ->getArrayResult();
    }

    //    /**
    //     * @return Budget[] Returns an array of Budget objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('b')
    //            ->andWhere('b.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('b.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Budget
    //    {
    //        return $this->createQueryBuilder('b')
    //            ->andWhere('b.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}