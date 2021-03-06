<?php

namespace App\Repository;

use App\Entity\MeterType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method MeterType|null find($id, $lockMode = null, $lockVersion = null)
 * @method MeterType|null findOneBy(array $criteria, array $orderBy = null)
 * @method MeterType[]    findAll()
 * @method MeterType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MeterTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MeterType::class);
    }

//    /**
//     * @return MeterType[] Returns an array of MeterType objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?MeterType
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
