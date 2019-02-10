<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Security\Core\User\UserInterface;

class User implements UserInterface
{
    private $id;

    private $email;

    private $password;

    private $firstName;

    private $lastName;

    private $roles;

    private $places;

    private $tariffs;

    private $tariffTypes;

    private $picture;

    private $profilePic;

    private $token;

    public $plainPassword;

    public $oldPassword;

    /**
     * @var boolean
     */
    private $enabled;

    /**
     * User constructor.
     */
    public function __construct()
    {
        $this->places = new ArrayCollection();
        $this->tariffs = new ArrayCollection();
        $this->tariffTypes = new ArrayCollection();
        $this->enabled = false;
    }

    /**
     * @return mixed
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * @return ArrayCollection
     */
    public function getPlaces(): Collection
    {
        return $this->places;
    }

    /**
     * @return mixed
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword(?string $password): void
    {
        $this->password = $password;
    }

    /**
     * @return null|string
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @return array
     */
    public function getRoles(): array
    {
        return $this->roles ? unserialize($this->roles) : ['ROLE_USER'];
    }

    public function eraseCredentials(): void
    {
    }

    /**
     * @return string
     */
    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     */
    public function setFirstName(string $firstName): void
    {
        $this->firstName = $firstName;
    }

    /**
     * @return mixed|null|string
     */
    public function getUsername()
    {
        return $this->getEmail();
    }

    /**
     * @param mixed $place
     */
    public function addPlace($place)
    {
        $this->places->add($place);
        // uncomment if you want to update other side
        $place->setUser($this);
    }

    /**
     * @param mixed $place
     */
    public function removePlace($place)
    {
        $this->places->removeElement($place);
        // uncomment if you want to update other side
        $place->setUser(null);
    }

    public function addRole($role): void
    {
        $roles = new ArrayCollection($this->getRoles());
        if (!$roles->contains($role)) {
            $roles[] = $role;
            $this->roles = serialize($roles->getValues());
        }
    }

    public function removeRole($role): void
    {
        $roles = new ArrayCollection($this->getRoles());
        if ($roles->contains($role)) {
            $roles->removeElement($roles);
            $this->roles = serialize($roles);
        }
    }

    public function hasRole(string $role): bool
    {
        return \in_array($role, $this->getRoles(), false);
    }

    public function __toString(): string
    {
        return $this->firstName.' '.$this->email;
    }

    /**
     * @param $tariff
     */
    public function addTariff($tariff)
    {
        $this->tariffs->add($tariff);
        // uncomment if you want to update other side
        $tariff->setUser($this);
    }

    /**
     * @param $tariff
     */
    public function removeTariff($tariff)
    {
        $this->tariffs->removeElement($tariff);
        // uncomment if you want to update other side
        $tariff->setUser(null);
    }

    public function getTariffs()
    {
        return $this->tariffs;
    }


    /**
     * @param $tariff
     */
    public function addTariffType($tariff)
    {
        $this->tariffTypes->add($tariff);
        // uncomment if you want to update other side
        $tariff->setUser($this);
    }

    /**
     * @param $tariff
     */
    public function removeTariffType($tariff)
    {
        $this->tariffTypes->removeElement($tariff);
        // uncomment if you want to update other side
        $tariff->setUser(null);
    }

    public function getTariffTypes()
    {
        return $this->tariffTypes;
    }

    /**
     * @return mixed
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param mixed $lastName
     */
    public function setLastName($lastName): void
    {
        $this->lastName = $lastName;
    }

    /**
     * @return mixed
     */
    public function getPicture()
    {
        return $this->picture;
    }

    /**
     * @param mixed $picture
     */
    public function setPicture($picture): void
    {
        $this->picture = $picture;
    }

    /**
     * @return mixed
     */
    public function getProfilePic()
    {
        return $this->profilePic;
    }

    /**
     * @param mixed $profilePic
     */
    public function setProfilePic($profilePic): void
    {
        $this->profilePic = $profilePic;
    }

    /**
     * @return mixed
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param mixed $token
     */
    public function setToken($token): void
    {
        $this->token = $token;
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * @param bool $enabled
     */
    public function setEnabled(bool $enabled): void
    {
        $this->enabled = $enabled;
    }

}
