<?php
declare(strict_types=1);

namespace App\Entity;

class ExchangeRate
{
    const BANKS = [
        self::MONOBANK => self::MONOBANK,
        self::NBU => self::NBU,
        self::PRIVAT => self::PRIVAT,
    ];

    const MONOBANK = 'mono';
    const NBU = 'nbu';
    const PRIVAT = 'privat';

    /** @var string */
    private $id;

    /** @var \DateTime */
    private $date;

    /** @var string */
    private $currency;

    /** @var float */
    private $sellRate;

    /** @var float */
    private $buyRate;

    /** @var string */
    private $bank;

    /** @var array */
    private $payload;

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return \DateTime
     */
    public function getDate(): \DateTime
    {
        return $this->date;
    }

    /**
     * @param \DateTime $date
     */
    public function setDate(\DateTime $date): void
    {
        $this->date = $date;
    }

    /**
     * @return array
     */
    public function getPayload(): array
    {
        return $this->payload;
    }

    /**
     * @param array $payload
     */
    public function setPayload(array $payload): void
    {
        $this->payload = $payload;
    }

    /**
     * @return string
     */
    public function getCurrency(): string
    {
        return $this->currency;
    }

    /**
     * @param string $currency
     */
    public function setCurrency(string $currency): void
    {
        $this->currency = $currency;
    }

    /**
     * @return float
     */
    public function getSellRate(): float
    {
        return $this->sellRate;
    }

    /**
     * @param float $sellRate
     */
    public function setSellRate(float $sellRate): void
    {
        $this->sellRate = $sellRate;
    }

    /**
     * @return float
     */
    public function getBuyRate(): float
    {
        return $this->buyRate;
    }

    /**
     * @param float $buyRate
     */
    public function setBuyRate(float $buyRate): void
    {
        $this->buyRate = $buyRate;
    }

    /**
     * @return string
     */
    public function getBank(): string
    {
        return $this->bank;
    }

    /**
     * @param string $bank
     */
    public function setBank(string $bank): void
    {
        $this->bank = $bank;
    }
}
