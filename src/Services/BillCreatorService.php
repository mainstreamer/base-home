<?php

namespace App\Services;

use App\Entity\Bill;
use App\Entity\Subscription;
use App\Repository\ExchangeRateRepository;
use App\Repository\SubscriptionRepository;
use Doctrine\ORM\EntityManagerInterface;

class BillCreatorService
{
    private $repository;

    private $entityManager;
    private $ratesFetcher;
    private $ratesRepo;

    public function __construct(SubscriptionRepository $repository, EntityManagerInterface $entityManager, RatesFetcherService $ratesFetcher, ExchangeRateRepository $repo)
    {
        $this->repository = $repository;
        $this->entityManager = $entityManager;
        $this->ratesFetcher = $ratesFetcher;
        $this->ratesRepo = $repo;
    }

    public function execute()
    {
        $subscriptions = $this->findBillableSubscriptions();

        foreach ($subscriptions as $subscription) {
            $this->createBill($subscription);
        }

        $this->entityManager->flush();
    }
    public function findBillableSubscriptions(): array
    {
        return $this->repository->findDueSubscriptions();
    }

    public function createBill(Subscription $subscription)
    {
        $bill = new Bill();
        $bill->setSubscription($subscription);
        $bill->setAmount($this->getAmount($subscription));
        $bill->setDate(new \DateTime());
        $bill->setIsPaid(false);
        $bill->setStatus('UNPAID');
        $this->setNextBillingDate($subscription);
        $this->entityManager->persist($bill);
    }

    public function setNextBillingDate(Subscription $subscription)
    {
        switch ($subscription->getType()) {
            case Subscription::ANNUAL: $subscription->setNextBillingDate($subscription->getNextBillingDate()->modify('+1 year')); break;
            case Subscription::MONTHLY: $subscription->setNextBillingDate($subscription->getNextBillingDate()->modify('+1 month')); break;
            case Subscription::DAILY: $subscription->setNextBillingDate($subscription->getNextBillingDate()->modify('+'.$subscription->getPeriod().' days')); break;
            default: break;
        }
    }

    public function getAmount(Subscription $subscription): float
    {
        $rate = $this->getBankRate($subscription->getCurrency(), 'privat');

        return $subscription->getPrice() * $rate;
    }

    public function getBankRate(string $bank, string $currency): float
    {
        // get rate for bank and curr
        $rate = $this->ratesRepo->getRateByBankAndCurrency($currency, $bank);

        return $rate->getSellRate();
    }
}
