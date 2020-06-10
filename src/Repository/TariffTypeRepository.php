<?php

namespace App\Repository;

use App\Entity\TariffType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TariffType|null find($id, $lockMode = null, $lockVersion = null)
 * @method TariffType|null findOneBy(array $criteria, array $orderBy = null)
 * @method TariffType[]    findAll()
 * @method TariffType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TariffTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TariffType::class);
    }

//    /**
//     * @return TariffType[] Returns an array of TariffType objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?TariffType
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
