<?php

namespace App\Entity;

class Indication
{
    private $id;

    private $name;

    private $value;

    private $meter;

    private $date;

    private $unit;

    private $file;

    private $textDate;

    public function __construct()
    {
        $this->date = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     */
    public function setValue($value): void
    {
        $this->value = $value;
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

    /**
     * @return mixed
     */
    public function getDate()
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
        return (string) $this->value;
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

}
