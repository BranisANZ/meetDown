<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ConferenceRepository")
 */
class Conference
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $location;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateEvent;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $image;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\RateConfUser", mappedBy="conference")
     */
    private $rateConfUsers;
    
    public function __construct()
    {
        $this->rateConfUsers = new ArrayCollection();
    }

    public function getId(): ?int
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

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(string $location): self
    {
        $this->location = $location;

        return $this;
    }

    public function getDateEvent(): ?\DateTimeInterface
    {
        return $this->dateEvent;
    }

    public function setDateEvent(\DateTimeInterface $date): self
    {
        $this->dateEvent = $date;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): self
    {
        $this->image = $image;

        return $this;
    }

    /**
     * @return Collection|RateConfUser[]
     */
    public function getRateConfUsers(): Collection
    {
        return $this->rateConfUsers;
    }

    public function addRateConfUser(RateConfUser $rateConfUser): self
    {
        if (!$this->rateConfUsers->contains($rateConfUser)) {
            $this->rateConfUsers[] = $rateConfUser;
            $rateConfUser->setConference($this);
        }

        return $this;
    }

    public function removeRateConfUser(RateConfUser $rateConfUser): self
    {
        if ($this->rateConfUsers->contains($rateConfUser)) {
            $this->rateConfUsers->removeElement($rateConfUser);
            // set the owning side to null (unless already changed)
            if ($rateConfUser->getConference() === $this) {
                $rateConfUser->setConference(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return (string)$this->getId();
    }

}
