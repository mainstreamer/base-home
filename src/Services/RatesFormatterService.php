<?php

namespace App\Services;

use App\Entity\ExchangeRate;

class RatesFormatterService
{
    /**
     * @param iterable $collection
     * @return string
     */
    public function formatForMessenger(iterable $collection): string
    {
        $result = '';
        /** @var ExchangeRate $item */
        foreach ($collection as $item) {
            $result.= "{$item->getBank()} {$item->getCurrency()}: {$item->getBuyRate()}/{$item->getSellRate()}".PHP_EOL;
        }

        return $result;
    }
}
