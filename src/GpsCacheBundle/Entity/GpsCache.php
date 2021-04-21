<?php

namespace Aroha\GpsCacheBundle\Entity;

use Aroha\GpsCacheBundle\Doctrine\Point;
use CrEOF\Spatial\DBAL\Types\Geography\PointType;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Aroha\GpsCacheBundle\Repository\GpsCacheRepository")
 */
class GpsCache
{

    public static $refreshSetting = [
        'invalid' => '+1 day',
        'valid' => '+1 year',
    ];

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $createOn;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $updateOn;

    /**
     * @ORM\Column(type="string", length=120, nullable=true)
     */
    private $street;

    /**
     * @ORM\Column(type="string", length=120, nullable=true)
     */
    private $number;

    /**
     * @ORM\Column(type="string", length=120, nullable=true)
     */
    private $city;

    /**
     * @ORM\Column(type="boolean")
     */
    private $valid = false;

    /**
     * @ORM\Column(type="string", length=200, nullable=true)
     */
    private $address;

    /**
     * @ORM\Column(type="point", nullable=false)
     */
    private $gps;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGps(): Point
    {
        return $this->gps;
    }

    public function setGps(Point $gps): self
    {
        $this->gps = $gps;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function setStreet(?string $street): self
    {
        $this->street = $street;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getValid(): ?bool
    {
        return $this->valid;
    }

    public function isValid(): ?bool
    {
        return $this->getValid();
    }

    public function setValid(bool $valid): self
    {
        $this->valid = $valid;

        return $this;
    }

    public function getCreateOn(): ?\DateTimeInterface
    {
        return $this->createOn;
    }

    public function setCreateOn(\DateTimeInterface $createOn): self
    {
        $this->createOn = $createOn;

        return $this;
    }

    public function getUpdateOn(): ?\DateTimeInterface
    {
        return $this->updateOn;
    }

    public function setUpdateOn(\DateTimeInterface $updateOn): self
    {
        $this->updateOn = $updateOn;

        return $this;
    }

    public function getNumber(): ?string
    {
        return $this->number;
    }

    public function setNumber(?string $number): self
    {
        $this->number = $number;

        return $this;
    }


}
