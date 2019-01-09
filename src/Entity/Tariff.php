<?php

namespace App\Entity;


class Tariff
{
    const DAY_TARIFF = 'day';

    const NIGHT_TARIFF = 'night';

    private $id;

    private $price;

    private $description;

    private $type;

    private $meter;

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param mixed $price
     */
    public function setPrice($price): void
    {
        $this->price = $price;
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
    public function getMeter()
    {
        return $this->meter;
    }

    /**
     * @param mixed $meter
     */
    public function setMeter($meter): void
    {
        $this->meter = $meter;
    }
}
