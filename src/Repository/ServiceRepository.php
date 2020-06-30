<?php

namespace App\Repository;

use App\Entity\Service;
use App\Entity\Subscription;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Subscription|null find($id, $lockMode = null, $lockVersion = null)
 * @method Subscription|null findOneBy(array $criteria, array $orderBy = null)
 * @method Subscription[]    findAll()
 * @method Subscription[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ServiceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Service::class);
    }

    // /**
    //  * @return Subscription[] Returns an array of Subscription objects
    //  */
    public function orderByNextBilling1(User $user)
    {
        return $this->createQueryBuilder('s')
            ->innerJoin('s.user', 'u')
            ->leftJoin('s.subscriptions', 'sub')
            ->orderBy('sub.nextBillingDate', 'ASC')
            ->andWhere('s.user = :val')
            ->setParameter('val', $user->getId())
            ->getQuery()
            ->getResult()
        ;
    }


    public function orderByNextBilling(User $user)
    {
        return $this->createQueryBuilder('s')
            ->innerJoin('s.user', 'u')
            ->leftJoin('s.subscriptions', 'sub')
            ->leftJoin('sub.bills', 'b')
            ->orderBy('b.payDate', 'ASC')
            ->orderBy('sub.nextBillingDate', 'ASC')
            ->andWhere('s.user = :val')
//            ->andWhere('s.user = :val and b.isPaid = :paid')
            ->setParameter('val', $user->getId())
//            ->setParameter('paid', true)
            ->getQuery()
            ->getResult()
        ;
    }

    public function orderByNextBillingDue(User $user)
    {
        return $this->createQueryBuilder('s')
            ->innerJoin('s.user', 'u')
            ->leftJoin('s.subscriptions', 'sub')
            ->leftJoin('sub.bills', 'b')
            ->orderBy('b.payDate', 'ASC')
            ->orderBy('sub.nextBillingDate', 'ASC')
//            ->andWhere('s.user = :val')
            ->andWhere('s.user = :val and b.isPaid = :paid')
            ->setParameter('val', $user->getId())
            ->setParameter('paid', false)
            ->getQuery()
            ->getResult()
            ;
    }

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
