<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Intl\Currencies;

class Subscription
{
    const TYPES = [
        self::ANNUAL => self::ANNUAL,
        self::MONTHLY => self::MONTHLY,
        self::DAILY => self::DAILY,
    ];

    const ANNUAL = 'annual';

    const MONTHLY = 'monthly';

    const DAILY = 'daily';

    /** @var string|null */
    private $id;

    /** @var \DateTime|null */
    private $startDate;

    /** @var \DateTime|null */
    private $endDate;

    /** @var int|null */
    private $period;

    /** @var int */
    private $price = 0;

    /** @var string */
    private $name = 'new subscription';

    /** @var string|null */
    private $description;

    /** @var bool */
    private $autoRenew = false;

    /** @var Service */
    private $service;

    /** @var string */
    private $type = 'daily';

    /** @var ArrayCollection */
    private $bills;

    /** @var string
     * https://www.iban.com/currency-codes
     */
    private $currency = 'UAH';

    /** @var \DateTime|null */
    private $nextBillingDate;

    /** @var Card|null */
    private $card;

    public function __construct()
    {
        $this->bills = new ArrayCollection();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * @return \DateTime|null
     */
    public function getStartDate(): ?\DateTime
    {
        return $this->startDate;
    }

    /**
     * @param \DateTime $startDate
     */
    public function setStartDate(\DateTime $startDate): void
    {
        $this->startDate = $startDate;
    }

    /**
     * @return \DateTime|null
     */
    public function getEndDate(): ?\DateTime
    {
        return $this->endDate;
    }

    /**
     * @param \DateTime|null $endDate
     */
    public function setEndDate(?\DateTime $endDate): void
    {
        $this->endDate = $endDate;
    }

    /**
     * @return int|null
     */
    public function getPeriod(): ?int
    {
        return $this->period;
    }

    /**
     * @param int|null $period
     */
    public function setPeriod(?int $period): void
    {
        $this->period = $period;
    }

    /**
     * @return int
     */
    public function getPrice(): int
    {
        return $this->price;
    }

    /**
     * @param int $price
     */
    public function setPrice(int $price): void
    {
        $this->price = $price;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string|null $description
     */
    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return bool
     */
    public function isAutoRenew(): bool
    {
        return $this->autoRenew;
    }

    /**
     * @param bool $autoRenew
     */
    public function setAutoRenew(bool $autoRenew): void
    {
        $this->autoRenew = $autoRenew;
    }

    /**
     * @return Service|null
     */
    public function getService(): ?Service
    {
        return $this->service;
    }

    /**
     * @param Service|null $service
     */
    public function setService(?Service $service): void
    {
        $this->service = $service;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type): void
    {
        if (!in_array($type, self::TYPES)) {
            throw new UnprocessableEntityHttpException('invalid sibscription type');
        }

        $this->type = $type;
    }

    /**
     * @param Bill $bill
     */
    private function addBill(Bill $bill): void
    {
        $this->bills[] = $bill;
    }

    /**
     * @param Bill $bill
     */
    private function removeBill(Bill $bill)
    {
        $this->bills->removeElement($bill);
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
        if (in_array($currency, Currencies::getCurrencyCodes())) {
            $this->currency = $currency;
        }
    }

    /**
     * @return \DateTime|null
     */
    public function getNextBillingDate(): ?\DateTime
    {
        return clone $this->nextBillingDate;
    }

    /**
     * @param \DateTime|null $nextBillingDate
     */
    public function setNextBillingDate(?\DateTime $nextBillingDate): void
    {
        $this->nextBillingDate = clone $nextBillingDate;
    }

    /**
     * @return Card|null
     */
    public function getCard(): ?Card
    {
        return $this->card;
    }

    /**
     * @param Card|null $card
     */
    public function setCard(?Card $card): void
    {
        $this->card = $card;
    }
}
