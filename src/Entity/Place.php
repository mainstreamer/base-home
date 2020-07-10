<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Security\Core\User\UserInterface;

class Place
{
    private $id;

    private $name;

    private $address;

    private $users;

    private $bills;

    private $meters;

    public function __construct()
    {
        $this->bills = new ArrayCollection();
        $this->meters = new ArrayCollection();
        $this->users = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return null|string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return Place
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * @param UserInterface|null $user
     * @return $this
     */
    public function addUser(?User $user): self
    {
        $this->users[] = $user;

        $user->addPlace($this);

        return $this;
    }

    /**
     * @param UserInterface|null $user
     * @return $this
     */
    public function removeUser(?UserInterface $user): self
    {
        $this->users->removeElement($user);

        return $this;
    }

    public function __toString(): string
    {
        return $this->name;
    }

    /**
     * @return ArrayCollection
     */
    public function getBills(): Collection
    {
        return $this->bills;
    }

    public function addBill(Bill $item): void
    {
        $this->bills[] = $item;
        $item->setPlace($this);
    }

    public function removeBill(Bill $item): void
    {
        $this->bills->remove($item);
        $item->setPlace(null);
    }

    /**
     * @return ArrayCollection
     */
    public function getMeters(): Collection
    {
        return $this->meters;
    }

    public function addMeter(Meter $item): void
    {
        $this->meters[] = $item;
        $item->setPlace($this);
    }

    public function removeMeter(Meter $item): void
    {
        $this->meters->remove($item);
        $item->setPlace(null);
    }

    /**
     * @return mixed
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param mixed $address
     */
    public function setAddress($address): void
    {
        $this->address = $address;
    }
}
