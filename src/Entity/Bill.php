<?php

declare(strict_types=1);

namespace App\Entity;

class Bill
{
    const STATUSES = [self::PAID, self::UNPAID];

    const PAID = 'PAID';

    const UNPAID = 'UNPAID';

    private $id;

    private $name;

    private $amount;

    private $place;

    private $period;

    private $date;

    private $status;

    public function __construct()
    {
        $this->date = new \DateTime();
        $this->status = self::UNPAID;
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
        if (in_array(self::STATUSES, $status)) {
            $this->status = $status;
        }
    }
}
