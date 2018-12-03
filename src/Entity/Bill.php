<?php

declare(strict_types=1);

namespace App\Entity;

class Bill
{
    const STATUSES = [self::PAID, self::UNPAID];

    const TYPES = [self::GAS, self::ELECTRICITY, self::HOT_WATER, self::COLD_WATER, self::HEATING];

    const GAS = 'gas';

    const ELECTRICITY = 'electricity';

    const HOT_WATER = 'hot water';

    const COLD_WATER = 'cold water';

    const HEATING = 'heating';

    const PAID = 'PAID';

    const UNPAID = 'UNPAID';

    private $id;

    private $name;

    private $amount;

    private $place;

    private $period;

    private $date;

    private $textDate;

    private $status;

    private $type;

    private $note;

    private $textPeriod;

    private $actuallyPaid;

    private $payDate;

    public function __construct()
    {
        $this->date = new \DateTime();
        $this->status = self::UNPAID;
        $this->textDate = $this->date->format('d-m-Y');
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getAmount(): ?int
    {
        return $this->amount;
    }

    public function setAmount(int $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getPlace()
    {
        return $this->place;
    }

    public function setPlace($place): void
    {
        $this->place = $place;
    }

    public function __toString(): string
    {
        return $this->name.' '.$this->amount;
    }

    /**
     * @return mixed
     */
    public function getPeriod()
    {
        return $this->period;
    }

    /**
     * @param mixed $period
     */
    public function setPeriod($period): void
    {
        $this->period = $period;
        if ($period) {
            setlocale(LC_ALL, 'uk_UA');
            $this->textPeriod = strftime('%b %Y', $period->getTimestamp());
        }
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
        $this->textDate = $date->format('d-m-Y');
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status): void
    {
        if (in_array($status, self::STATUSES)) {
            $this->status = $status;
        }
    }

    /**
     * @return mixed
     */
    public function getTextDate()
    {
        return $this->textDate;
    }

    /**
     * @param mixed $textDate
     */
    public function setTextDate($textDate): void
    {
        $this->textDate = $textDate;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type): void
    {
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * @param mixed $note
     */
    public function setNote($note): void
    {
        $this->note = $note;
    }

    /**
     * @return mixed
     */
    public function getTextPeriod()
    {
        return $this->textPeriod;
    }

    /**
     * @param mixed $textPeriod
     */
    public function setTextPeriod($textPeriod): void
    {
        $this->textPeriod = $textPeriod;
    }

    /**
     * @return mixed
     */
    public function getActuallyPaid()
    {
        return $this->actuallyPaid;
    }

    /**
     * @param mixed $actuallyPaid
     */
    public function setActuallyPaid($actuallyPaid): void
    {
        $this->actuallyPaid = $actuallyPaid;
    }

    /**
     * @return mixed
     */
    public function getPayDate()
    {
        return $this->payDate;
    }

    /**
     * @param mixed $payDate
     */
    public function setPayDate($payDate): void
    {
        $this->payDate = $payDate;
    }
}
