<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;

class Card
{
    const TYPES = [
        self::VISA,
        self::MASTERCARD,
    ];

    const VISA = 'visa';

    const MASTERCARD = 'mastercard';

    /** @var string */
    private $id;

    /** @var string */
    private $name;

    /** @var string */
    private $type;

    /** @var string|null */
    private $digits;

    /** @var bool */
    private $active = true;

    /** @var $subscriptions ArrayCollection */
    private $subscriptions;

    public function __construct()
    {
        $this->subscriptions = new ArrayCollection();
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

    public function __toString(): string
    {
        return 'Card '.$this->type.' '.$this->digits;
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
     * @return ArrayCollection
     */
    public function getSubscriptions(): ArrayCollection
    {
        return $this->subscriptions;
    }

    /**
     * @param Subscription $subscription
     */
    public function addSubscription(Subscription $subscription): void
    {
        $this->subscriptions[] = $subscription;
    }

    /**
     * @param Subscription $subscription
     */
    public function removeSubscription(Subscription $subscription): void
    {
        $this->subscriptions->removeElement($subscription);
    }

    /**
     * @return string|null
     */
    public function getDigits(): ?string
    {
        return $this->digits;
    }

    /**
     * @param string|null $digits
     */
    public function setDigits(?string $digits): void
    {
        $this->digits = $digits;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->active;
    }

    /**
     * @param bool $active
     */
    public function setActive(bool $active): void
    {
        $this->active = $active;
    }
}
