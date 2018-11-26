<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;

class Meter
{
    private $id;

    private $name;

    private $type;

    private $unit;

    private $place;

    private $indications;

    public function __construct()
    {
        $this->indications = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getType(): ?MeterType
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType(?MeterType $type): void
    {
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function getPlace()
    {
        return $this->place;
    }

    /**
     * @param mixed $place
     */
    public function setPlace($place): void
    {
        $this->place = $place;
    }

    /**
     * @return Collection
     */
    public function getIndications(): Collection
    {
        $criteria = Criteria::create()->orderBy(['date' => Criteria::DESC]);

        return $this->indications->matching($criteria);
    }

    public function addIndication(Indication $item): void
    {
        $this->indications[] = $item;
        $item->setMeter($this);
    }

    public function removeMeter(Indication $item): void
    {
        $this->indications->remove($item);
        $item->setMeter(null);
    }

    /**
     * @return mixed
     */
    public function getUnit()
    {
        return $this->unit;
    }

    /**
     * @param mixed $unit
     */
    public function setUnit($unit): void
    {
        $this->unit = $unit;
    }

    public function __toString()
    {
        return $this->name;
    }
}
