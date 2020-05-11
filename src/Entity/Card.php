<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\UserInterface;

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

    /** @var string */
    private $digits;

    /** @var bool */
    private $active = true;

    /** @var ArrayCollection */
    private $subscriptions;

    /** @var string */
    private $bank;

    /** @var string */
    private $firstDigits;

    /** @var User */
    private $user;

    /** @var string */
    private $currency = 'UAH';

    /** @var ?string */
    private $description;

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
        return $this->digits;
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

    /**
     * @return string
     */
    public function getBank(): ?string
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

    /**
     * @return string
     */
    public function getFirstDigits(): ?string
    {
        return $this->firstDigits;
    }

    /**
     * @param string $firstDigits
     */
    public function setFirstDigits(string $firstDigits): void
    {
        $this->firstDigits = $firstDigits;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param UserInterface $user
     * @return $this
     */
    public function setUser(UserInterface $user): self
    {
        $this->user = $user;

        return $this;
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
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description): void
    {
        $this->description = $description;
    }
}
