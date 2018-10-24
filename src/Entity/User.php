<?php declare(strict_types = 1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class User
 * @package App\Entity
 * @ORM\Entity
 * @UniqueEntity (fields="email", message="Email already taken")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    private $email;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(max=4096)
     */
    public $plainPassword;

    /**
     * The below length depends on the "algorithm" you use for encoding
     * the password, but this works well with bcrypt.
     *
     * @ORM\Column(type="string", length=64)
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\NotBlank()
     * @Assert\Length(max=4096)
     */
    private $firstName;

    /**
     * @ORM\Column(type="array")
     */
    private $roles;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="App\Entity\Place", mappedBy="user")
     */
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
    public function getPlaces(): ArrayCollection
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
    public function getRoles(): Collection
    {
//        return $this->roles;
        return $this->roles ? new ArrayCollection(unserialize($this->roles)) : new ArrayCollection(['ROLE_USER']);
    }

    /**
     * @return void
     */
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
        //$place->setUser($this);
    }

    /**
     * @param mixed $place
     */
    public function removePlace($place)
    {
        $this->places->removeElement($place);
        // uncomment if you want to update other side
        //$place->setUser(null);
    }

    public function addRole($role): void
    {
        $roles = $this->getRoles();
        if (!$roles->contains($role)) {
            $roles[] = $role;
            $this->roles = serialize($roles);
        }
    }

    public function removeRole($role): void
    {
        $roles = $this->getRoles();
        if ($roles->contains($role)) {
            $roles->removeElement($roles);
            $this->roles = serialize($roles);
        }
    }

    public function __toString(): string
    {
        return $this->firstName." ".$this->email;
    }

}
