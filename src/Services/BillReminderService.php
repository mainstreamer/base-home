<?php

namespace App\Services;

use App\Entity\Subscription;
use App\Repository\ExchangeRateRepository;
use App\Repository\SubscriptionRepository;
use Doctrine\ORM\EntityManagerInterface;

class BillReminderService
{
    private $repository;
    private $entityManager;
    private $ratesFetcher;
    private $ratesRepo;
    private $messageSender;
    private $remindBeforeDays = 0;

    public function __construct(SubscriptionRepository $repository, EntityManagerInterface $entityManager, RatesFetcherService $ratesFetcher, ExchangeRateRepository $repo, MessageSenderService $messageSender)
    {
        $this->repository = $repository;
        $this->entityManager = $entityManager;
        $this->ratesFetcher = $ratesFetcher;
        $this->ratesRepo = $repo;
        $this->messageSender = $messageSender;
    }

    public function execute()
    {
        $subscriptions = $this->repository->findSubscriptionsDue($this->remindBeforeDays);
        if (!$subscriptions) {
            return;
        }
        $message = $this->createMessage($subscriptions);
        $this->messageSender->sendMessage($message);
    }

    public function findBillableSubscriptions(): array
    {
        return $this->repository->findSubscriptionsDue(1);
    }

    public function setRemindBeforeDays(int $days): void
    {
        $this->remindBeforeDays = $days;
    }

    protected function createMessage(array $subscriptions): string
    {
        $message = '';
        /** @var Subscription $subscription */
        foreach ($subscriptions as $subscription) {
            $price = $subscription->getPrice()/100;
            $message .= 'Due tomorrow: ' .$subscription->getService()->getName().' '.$subscription->getName().' '.$subscription->getCurrency(). ' '.$price.PHP_EOL;
        }

        return $message;
    }
}
