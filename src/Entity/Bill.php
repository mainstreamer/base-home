<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class Bill
{
    const STATUSES = [self::PAID, self::UNPAID];

    const TYPES = [self::COMMUNAL, self::ELECTRICITY, self::GAS, self::HOT_WATER, self::COLD_WATER, self::HEATING];

    const GAS = 'gas';

    const ELECTRICITY = 'electricity';

    const HOT_WATER = 'hot water';

    const COLD_WATER = 'cold water';

    const HEATING = 'heating';

    const PAID = 'PAID';

    const UNPAID = 'UNPAID';

    const COMMUNAL = 'communal';

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

    private $payDateText;

    private $file;

    private $isPaid;

    private $files;

    /** @var $subscription Subscription|null */
    private $subscription;

    public function __construct()
    {
        $this->date = new \DateTime();
        $this->status = self::UNPAID;
        $this->textDate = $this->date->format('d-m-Y');
        $this->setIsPaid(false);
        $this->files = new ArrayCollection();

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

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount($amount): self
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
            $value = strftime('%b %Y', $period->getTimestamp());
//            $this->textPeriod = strftime('%b %Y', $period->getTimestamp());

            $this->textPeriod = mb_strtoupper(mb_substr($value, 0, 1)).mb_substr($value, 1);
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
    public function setPayDate(?\DateTime $payDate): void
    {
        $this->payDate = $payDate;
        $this->payDateText = is_object($payDate) ? $payDate->format('d-m-Y') : null;
    }

    /**
     * @return mixed
     */
    public function getPayDateText()
    {
        return $this->payDateText;
    }

    /**
     * @param mixed $payDateText
     */
    public function setPayDateText($payDateText): void
    {
        $this->payDateText = $payDateText;
    }

    /**
     * @return mixed
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param mixed $file
     */
    public function setFile($file): void
    {
        $this->file = $file;
    }

    /**
     * @return mixed
     */
    public function getisPaid(): bool
    {
        return $this->isPaid;
    }

    /**
     * @param mixed $isPaid
     */
    public function setIsPaid(bool $isPaid): void
    {
        $this->isPaid = $isPaid;
    }

    /**
     * @return ArrayCollection
     */
    public function getFiles(): Collection
    {
        return $this->files;
    }

    public function addFile(FileUpload $item): void
    {
        $this->files[] = $item;
        $item->setBill($this);
    }

    public function removeFile(FileUpload $item): void
    {
        $this->files->removeElement($item);
        $item->setBill(null);
    }

    public function setFiles($files)
    {
        $this->files = $files;
    }

    /**
     * @return Subscription|null
     */
    public function getSubscription(): ?Subscription
    {
        return $this->subscription;
    }

    /**
     * @param Subscription|null $subscription
     */
    public function setSubscription(?Subscription $subscription): void
    {
        $this->subscription = $subscription;
    }
}
