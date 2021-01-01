<?php

namespace App\Repository;

use App\Entity\Subscription;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Subscription|null find($id, $lockMode = null, $lockVersion = null)
 * @method Subscription|null findOneBy(array $criteria, array $orderBy = null)
 * @method Subscription[]    findAll()
 * @method Subscription[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SubscriptionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Subscription::class);
    }

    /**
     * @return int|mixed|string
     * @deprecated
     */
    public function findDueSubscriptions()
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.nextBillingDate BETWEEN :val AND :val1')
            ->setParameter('val', (new \DateTime())->format('Y-m-d').' 00:00:00')
            ->setParameter('val1', (new \DateTime())->format('Y-m-d').' 23:59:59')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findSubscriptionsDue(int $daysBefore = 0)
    {
        if ($daysBefore) {
            $daysString = '+'.$daysBefore.' days';
        } else {
            $daysString = 'now';
        }

        return $this->createQueryBuilder('s')
            ->andWhere('s.nextBillingDate BETWEEN :val AND :val1')
            ->setParameter('val', (new \DateTimeImmutable($daysString))->format('Y-m-d').' 00:00:00')
            ->setParameter('val1', (new \DateTimeImmutable($daysString))->format('Y-m-d').' 23:59:59')
            ->getQuery()
            ->getResult()
        ;
    }

    // /**
    //  * @return Subscription[] Returns an array of Subscription objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Subscription
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
