<?php

namespace App\Repository;

use App\Entity\ExchangeRate;
use App\Entity\Subscription;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Subscription|null find($id, $lockMode = null, $lockVersion = null)
 * @method Subscription|null findOneBy(array $criteria, array $orderBy = null)
 * @method Subscription[]    findAll()
 * @method Subscription[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ExchangeRateRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ExchangeRate::class);
    }

    /**
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findLastRecord()
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.currency = :usd')
            ->orderBy('e.date', 'DESC')
            ->setMaxResults(1)
            ->setParameter('usd', 'USD')
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function getLatest()
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.date BETWEEN :val AND :val1')
            ->orderBy('e.bank', 'ASC')
            ->setParameter('val', (new \DateTime())->format('Y-m-d').' 00:00:00')
            ->setParameter('val1', (new \DateTime())->format('Y-m-d').' 23:59:59')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @param string $currency
     * @return ExchangeRate
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getRate(string $currency): ExchangeRate
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.currency = :usd')
            ->orderBy('e.date', 'DESC')
            ->setMaxResults(1)
            ->setParameter('usd', $currency)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * @param string $currency
     * @return ExchangeRate
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getRateByBankAndCurrency(string $currency, string $bank): ?ExchangeRate
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.currency = :usd')
            ->andWhere('e.bank = :bank')
            ->orderBy('e.date', 'DESC')
            ->setMaxResults(1)
            ->setParameter('usd', $currency)
            ->setParameter('bank', $bank)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
