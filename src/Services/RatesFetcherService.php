<?php

namespace App\Services;

use App\Entity\ExchangeRate;
use App\Repository\ExchangeRateRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpClient\HttpClient;

class RatesFetcherService
{
    private $repository;

    private $entityManager;

    public function __construct(ExchangeRateRepository $repository, EntityManagerInterface $entityManager)
    {
        $this->repository = $repository;
        $this->entityManager = $entityManager;
    }

    public function execute()
    {
        return $this->fetchOrGet();
    }

    private function fetchOrGet()
    {
        $rates = $this->repository->getLatest();
        if (null !== $rates && !empty($rates) && $rates[0]->getDate()->format('d-m-Y') === (new \DateTime())->format('d-m-Y')) {
            return $rates;
        } else {
            try {
                $this->fetchAndSave();
            } catch (\Exception $e) {
                return $rates;
            }

            return $this->repository->getLatest();
        }
    }

    private function fetchAndSave()
    {
        $client = HttpClient::create();
        $response = $client->request('GET', 'http://bank.gov.ua/NBUStatService/v1/statdirectory/exchange?json');
        $result = $response->toArray();

        $nbuExchangeRateEur = new ExchangeRate();
        $nbuExchangeRateEur->setCurrency('EUR');
        $nbuExchangeRateEur->setBank(ExchangeRate::NBU);
        $nbuExchangeRateEur->setDate(new \DateTime());
        $nbuExchangeRateEur->setBuyRate(round((float) $result[33]['rate'], 2));
        $nbuExchangeRateEur->setSellRate(round((float) $result[33]['rate'], 2));
        $this->entityManager->persist($nbuExchangeRateEur);

        $nbuExchangeRateUsd = new ExchangeRate();
        $nbuExchangeRateUsd->setCurrency('USD');
        $nbuExchangeRateUsd->setBank(ExchangeRate::NBU);
        $nbuExchangeRateUsd->setDate(new \DateTime());
        $nbuExchangeRateUsd->setBuyRate(round((float) $result[26]['rate'], 2));
        $nbuExchangeRateUsd->setSellRate(round((float) $result[26]['rate'], 2));
        $this->entityManager->persist($nbuExchangeRateUsd);

        $response = $client->request('GET', 'http://api.privatbank.ua/p24api/pubinfo?exchange&json&coursid=11');
        $result = $response->toArray();
        $privatRates['USD'] = round($result[0]['buy'], 2).' / '.round($result[0]['sale'], 2);
        $privatRates['EUR'] = round($result[1]['buy'], 2).' / '.round($result[1]['sale'], 2);

        $privatExchangeRateUsd = new ExchangeRate();
        $privatExchangeRateUsd->setCurrency('USD');
        $privatExchangeRateUsd->setBank(ExchangeRate::PRIVAT);
        $privatExchangeRateUsd->setDate(new \DateTime());
        $privatExchangeRateUsd->setBuyRate(round((float) $result[0]['buy'], 2));
        $privatExchangeRateUsd->setSellRate(round((float) $result[0]['sale'], 2));
        $this->entityManager->persist($privatExchangeRateUsd);

        $privatExchangeRateEur = new ExchangeRate();
        $privatExchangeRateEur->setCurrency('EUR');
        $privatExchangeRateEur->setBank(ExchangeRate::PRIVAT);
        $privatExchangeRateEur->setDate(new \DateTime());
        $privatExchangeRateEur->setBuyRate(round((float) $result[1]['buy']), 2);
        $privatExchangeRateEur->setSellRate(round((float) $result[1]['sale'], 2));
        $this->entityManager->persist($privatExchangeRateEur);

        $response = $client->request('GET', 'https://api.monobank.ua/bank/currency');
        $result = $response->toArray();
        $monoRates['USD'] = round($result[0]['rateBuy'], 2).' / '.round($result[0]['rateSell'], 2);
        $monoRates['EUR'] = round($result[1]['rateBuy'], 2).' / '.round($result[1]['rateSell'], 2);

        $monoExchangeRateEur = new ExchangeRate();
        $monoExchangeRateEur->setCurrency('EUR');
        $monoExchangeRateEur->setBank(ExchangeRate::MONOBANK);
        $monoExchangeRateEur->setDate(new \DateTime());
        $monoExchangeRateEur->setBuyRate(round((float) $result[1]['rateBuy'], 2));
        $monoExchangeRateEur->setSellRate(round((float) $result[1]['rateSell'], 2));
        $this->entityManager->persist($monoExchangeRateEur);

        $monoExchangeRateUsd = new ExchangeRate();
        $monoExchangeRateUsd->setCurrency('USD');
        $monoExchangeRateUsd->setBank(ExchangeRate::MONOBANK);
        $monoExchangeRateUsd->setDate(new \DateTime());
        $monoExchangeRateUsd->setBuyRate(round((float) $result[0]['rateBuy'], 2));
        $monoExchangeRateUsd->setSellRate(round((float) $result[0]['rateSell'], 2));
        $this->entityManager->persist($monoExchangeRateUsd);

        $this->entityManager->flush();
    }
}
