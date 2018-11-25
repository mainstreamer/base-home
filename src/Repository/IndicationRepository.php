<?php

namespace App\Repository;

use App\Entity\Indication;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Indication|null find($id, $lockMode = null, $lockVersion = null)
 * @method Indication|null findOneBy(array $criteria, array $orderBy = null)
 * @method Indication[]    findAll()
 * @method Indication[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class IndicationRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Indication::class);
    }

//    /**
//     * @return Indication[] Returns an array of Indication objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('i.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Indication
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
