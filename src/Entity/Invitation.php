<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\InvitationRepository")
 */
class Invitation
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     */
    private $from;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     */
    private $to;

    /**
     * @ORM\Column(name="title", type="string", length=255, nullable=true)
     */
    private $title;

    /**
     * @ORM\Column(name="message", type="text", nullable=true)
     */
    private $message;



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getFrom(): ?User
    {
        return $this->from;
    }

    public function setFrom(?User $from): self
    {
        $this->from = $from;

        return $this;
    }

    public function getTo(): ?User
    {
        return $this->to;
    }

    public function setTo(?User $to): self
    {
        $this->to = $to;

        return $this;
    }
}
