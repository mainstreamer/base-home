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

    public $plainPassword;

    private $password;

    private $firstName;

    private $roles;

    private $places;

    /**
     * User constructor.
     */
    public function __construct()
    {
        $this->places = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getId()
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
    public function setPassword(string $password): void
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
}
