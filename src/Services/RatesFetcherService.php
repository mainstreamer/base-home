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
        $rate = $this->repository->findLastRecord();
        if (null !== $rate && $rate->getDate()->format('d-m-Y') === (new \DateTime())->format('d-m-Y')) {
            return $rate;
        } else {
            return $this->fetchAndSave();
        }
    }

    private function fetchAndSave()
    {
        $client = HttpClient::create();

        $response = $client->request('GET', 'http://bank.gov.ua/NBUStatService/v1/statdirectory/exchange?json');
        $result = $response->toArray();
        $eur = $result[33];
        $usd = $result[26];
        $rates = ['EUR' => $eur['rate'], 'USD' => $usd['rate'], 'UAH' => 1];

        $response = $client->request('GET', 'http://api.privatbank.ua/p24api/pubinfo?exchange&json&coursid=11');
        $result = $response->toArray();
        $privatRates['USD'] = round($result[0]['buy'], 2).' / '.round($result[0]['sale'], 2);
        $privatRates['EUR'] = round($result[1]['buy'], 2).' / '.round($result[1]['sale'], 2);

        $response = $client->request('GET', 'https://api.monobank.ua/bank/currency');
        $result = $response->toArray();
        $monoRates['USD'] = round($result[0]['rateBuy'], 2).' / '.round($result[0]['rateSell'], 2);
        $monoRates['EUR'] = round($result[1]['rateBuy'], 2).' / '.round($result[1]['rateSell'], 2);

        $rate = new ExchangeRate();
        $rate->setDate(new \DateTime());
        $rate->setPayload(['nbu' => $rates, 'privat' => $privatRates, 'mono' => $monoRates]);
        $this->entityManager->persist($rate);
        $this->entityManager->flush();

        return $rate;
    }
}
