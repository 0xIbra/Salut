<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\EventRepository")
 * @UniqueEntity(
 *     fields={"uniqueId"},
 *     errorPath="uniqueId",
 *     message="event.unique"
 * )
 * @UniqueEntity(
 *     fields={"publicId"},
 *     errorPath="publicId",
 *     message="event.publicId.unique"
 * )
 */
class Event
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Serializer\Groups({"public", "profile", "admin"})
     */
    private $id;

    /**
     * @ORM\Column(name="unique_id", type="string", length=255, nullable=false)
     * @Serializer\Groups({"profile"})
     * @Serializer\SerializedName("unique_id")
     */
    private $uniqueId;


    /**
     * @ORM\Column(name="public_id", type="string", length=50, nullable=true, unique=true)
     * @Serializer\Groups({"public", "profile", "admin"})
     * @Serializer\SerializedName("public_id")
     */
    private $publicId;

    /**
     * @ORM\Column(type="string", length=255)
     * @Serializer\Groups({"public", "profile", "admin"})
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Serializer\Groups({"public", "profile", "admin"})
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity="Location", cascade={"persist", "remove"})
     * @Serializer\Groups({"public", "profile", "admin"})
     */
    private $location;

    /**
     * @ORM\Column(type="datetime")
     * @Serializer\Groups({"public", "profile", "admin"})
     */
    private $start;

    /**
     * @ORM\Column(type="datetime")
     * @Serializer\Groups({"public", "profile", "admin"})
     */
    private $end;

    /**
     * @ORM\Column(type="integer")
     * @Serializer\Groups({"public", "profile", "admin"})
     */
    private $spots;

    /**
     * @ORM\ManyToOne(targetEntity="Image", cascade={"persist", "remove"})
     * @Serializer\Groups({"public", "profile", "admin"})
     */
    private $image;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="events")
     * @Assert\NotNull()
     * @Serializer\Groups({"public", "profile", "admin"})
     */
    private $organizer;

    /**
     * @ORM\OneToMany(targetEntity="Program", mappedBy="event", cascade={"persist", "remove"})
     * @Serializer\Groups({"public", "profile", "admin"})
     */
    private $programs;


    /**
     * @ORM\OneToMany(targetEntity="Invitation", mappedBy="event", cascade={"persist", "remove"})
     */
    private $invitations;


    /**
     * @ORM\Column(type="boolean")
     * @Serializer\Groups({"public", "profile", "admin"})
     */
    private $enabled;


    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Serializer\Groups({"public", "profile", "admin"})
     */
    private $createdAt;


    public function __construct()
    {
        $this->uniqueId = uniqid('salut_', true);
        $this->publicId = uniqid('', true);
        $this->enabled = false;
        $this->createdAt = new \DateTime();
        $this->programs = new ArrayCollection();
        $this->invitations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getStart(): ?\DateTimeInterface
    {
        return $this->start;
    }

    public function setStart(\DateTimeInterface $start): self
    {
        $this->start = $start;

        return $this;
    }

    public function getEnd(): ?\DateTimeInterface
    {
        return $this->end;
    }

    public function setEnd(\DateTimeInterface $end): self
    {
        $this->end = $end;

        return $this;
    }

    public function getSpots(): ?int
    {
        return $this->spots;
    }

    public function setSpots(int $spots): self
    {
        $this->spots = $spots;

        return $this;
    }

    public function getEnabled(): ?bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getLocation(): ?Location
    {
        return $this->location;
    }

    public function setLocation(?Location $location): self
    {
        $this->location = $location;

        return $this;
    }

    /**
     * @return Collection|Program[]
     */
    public function getPrograms(): Collection
    {
        return $this->programs;
    }

    public function addProgram(Program $program): self
    {
        if (!$this->programs->contains($program)) {
            $this->programs[] = $program;
            $program->setEvent($this);
        }

        return $this;
    }

    public function removeProgram(Program $program): self
    {
        if ($this->programs->contains($program)) {
            $this->programs->removeElement($program);
            // set the owning side to null (unless already changed)
            if ($program->getEvent() === $this) {
                $program->setEvent(null);
            }
        }

        return $this;
    }

    public function getOrganizer(): ?User
    {
        return $this->organizer;
    }

    public function setOrganizer(?User $organizer): self
    {
        $this->organizer = $organizer;

        return $this;
    }

    public function getUniqueId(): ?string
    {
        return $this->uniqueId;
    }

    public function setUniqueId(string $uniqueId): self
    {
        $this->uniqueId = $uniqueId;

        return $this;
    }

    public function getImage(): ?Image
    {
        return $this->image;
    }

    public function setImage(?Image $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getPublicId(): ?string
    {
        return $this->publicId;
    }

    public function setPublicId(?string $publicId): self
    {
        $this->publicId = $publicId;

        return $this;
    }

    /**
     * @return Collection|Invitation[]
     */
    public function getInvitations(): Collection
    {
        return $this->invitations;
    }

    public function addInvitation(Invitation $invitation): self
    {
        if (!$this->invitations->contains($invitation)) {
            $this->invitations[] = $invitation;
            $invitation->setEvent($this);
        }

        return $this;
    }

    public function removeInvitation(Invitation $invitation): self
    {
        if ($this->invitations->contains($invitation)) {
            $this->invitations->removeElement($invitation);
            // set the owning side to null (unless already changed)
            if ($invitation->getEvent() === $this) {
                $invitation->setEvent(null);
            }
        }

        return $this;
    }
}
