<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\MaxDepth;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity(
 *     fields={"email"},
 *     errorPath="email",
 *     message="email.unique"
 * )
 */
class User implements UserInterface
{

    const ROLE_ADMIN = 'Administrateur';
    const ROLE_SUPER_ADMIN = 'Administrateur';
    const ROLE_ORGANIZER = 'Organisateur';
    const ROLE_PARTICIPANT = 'Participant';
    const ROLE_SPEAKER = 'Intervenant';
    const ROLE_MANAGER = 'Gérant';


    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"profile", "admin", "public"})
     */
    private $id;

    /**
     * @ORM\Column(name="first_name", type="string", length=50, nullable=false)
     * @Assert\NotBlank(message="first_name.required")
     * @Groups({"profile", "admin", "public"})
     */
    private $firstName;

    /**
     * @ORM\Column(name="last_name", type="string", length=100, nullable=false)
     * @Assert\NotBlank(message="last_name.required")
     * @Groups({"profile", "admin", "public"})
     */
    private $lastName;

    /**
     * @ORM\Column(name="occupation", type="string", length=100, nullable=true)
     * @Groups({"profile", "admin", "public"})
     */
    private $occupation;

    /**
     * @ORM\Column(name="bio", type="string", length=255, nullable=true)
     * @Groups({"profile", "admin", "public"})
     */
    private $bio;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Assert\NotBlank(message="email.required")
     * @Assert\Email(message="email.invalid")
     * @Groups({"profile", "admin", "public"})
     */
    private $email;

    /**
     * @ORM\Column(name="is_active", type="boolean", nullable=false)
     * @Groups({"profile", "admin"})
     */
    private $isActive;


    /**
     * @ORM\ManyToOne(targetEntity="Image", cascade={"persist", "remove"})
     * @Groups({"profile", "admin", "public"})
     */
    private $image;

    
    /**
     * @ORM\Column(type="json")
     * @Groups({"profile", "admin"})
     */
    private $roles = [];

    /**
     * @ORM\Column(name="confirmation_token", type="string", length=255, nullable=true)
     */
    private $confirmationToken;


    /**
     * @ORM\Column(name="last_login", type="datetime", nullable=true)
     * @Groups({"profile", "admin", "public"})
     */
    private $lastLogin;


    /**
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     * @Groups({"profile", "admin"})
     */
    private $createdAt;

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     * @Assert\NotBlank(message="password.required")
     * @Assert\Length(min="8", minMessage="password.min")
     */
    private $password;


    /**
     * @ORM\OneToMany(targetEntity="Participant", mappedBy="user", cascade={"persist", "remove"})
     * @MaxDepth(2)
     * @Groups({"profile", "admin"})
     */
    private $participations;


    /**
     * @ORM\OneToMany(targetEntity="Event", mappedBy="organizer", cascade={"persist", "remove"})
     * @Groups({"profile", "admin"})
     */
    private $events;


    /**
     * @ORM\OneToMany(targetEntity="Notification", mappedBy="to", cascade={"persist", "remove"})
     */
    private $notifications;

    /**
     * @ORM\OneToMany(targetEntity="Invitation", mappedBy="from", cascade={"persist", "remove"})
     */
    private $invitationsSent;


    /**
     * @ORM\OneToMany(targetEntity="Invitation", mappedBy="to", cascade={"persist"})
     */
    private $invitationsReceived;



    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->isActive = false;
        $this->participations = new ArrayCollection();
        $this->events = new ArrayCollection();
        $this->invitationsSent = new ArrayCollection();
        $this->invitationsReceived = new ArrayCollection();
        $this->notifications = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getOccupation(): ?string
    {
        return $this->occupation;
    }

    public function setOccupation(string $occupation): self
    {
        $this->occupation = $occupation;

        return $this;
    }

    public function getBio(): ?string
    {
        return $this->bio;
    }

    public function setBio(?string $bio): self
    {
        $this->bio = $bio;

        return $this;
    }

    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function getLastLogin(): ?\DateTimeInterface
    {
        return $this->lastLogin;
    }

    public function setLastLogin(?\DateTimeInterface $lastLogin): self
    {
        $this->lastLogin = $lastLogin;

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

    public function getConfirmationToken(): ?string
    {
        return $this->confirmationToken;
    }

    public function setConfirmationToken(?string $confirmationToken): self
    {
        $this->confirmationToken = $confirmationToken;

        return $this;
    }

    /**
     * @return Collection|Participant[]
     */
    public function getParticipations(): Collection
    {
        return $this->participations;
    }

    public function addParticipation(Participant $participation): self
    {
        if (!$this->participations->contains($participation)) {
            $this->participations[] = $participation;
            $participation->setUser($this);
        }

        return $this;
    }

    public function removeParticipation(Participant $participation): self
    {
        if ($this->participations->contains($participation)) {
            $this->participations->removeElement($participation);
            // set the owning side to null (unless already changed)
            if ($participation->getUser() === $this) {
                $participation->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Event[]
     */
    public function getEvents(): Collection
    {
        return $this->events;
    }

    public function addEvent(Event $event): self
    {
        if (!$this->events->contains($event)) {
            $this->events[] = $event;
            $event->setOrganizer($this);
        }

        return $this;
    }

    public function removeEvent(Event $event): self
    {
        if ($this->events->contains($event)) {
            $this->events->removeElement($event);
            // set the owning side to null (unless already changed)
            if ($event->getOrganizer() === $this) {
                $event->setOrganizer(null);
            }
        }

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

    /**
     * @return Collection|Invitation[]
     */
    public function getInvitationsSent(): Collection
    {
        return $this->invitationsSent;
    }

    public function addInvitationsSent(Invitation $invitationsSent): self
    {
        if (!$this->invitationsSent->contains($invitationsSent)) {
            $this->invitationsSent[] = $invitationsSent;
            $invitationsSent->setFrom($this);
        }

        return $this;
    }

    public function removeInvitationsSent(Invitation $invitationsSent): self
    {
        if ($this->invitationsSent->contains($invitationsSent)) {
            $this->invitationsSent->removeElement($invitationsSent);
            // set the owning side to null (unless already changed)
            if ($invitationsSent->getFrom() === $this) {
                $invitationsSent->setFrom(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Invitation[]
     */
    public function getInvitationsReceived(): Collection
    {
        return $this->invitationsReceived;
    }

    public function addInvitationsReceived(Invitation $invitationsReceived): self
    {
        if (!$this->invitationsReceived->contains($invitationsReceived)) {
            $this->invitationsReceived[] = $invitationsReceived;
            $invitationsReceived->setTo($this);
        }

        return $this;
    }

    public function removeInvitationsReceived(Invitation $invitationsReceived): self
    {
        if ($this->invitationsReceived->contains($invitationsReceived)) {
            $this->invitationsReceived->removeElement($invitationsReceived);
            // set the owning side to null (unless already changed)
            if ($invitationsReceived->getTo() === $this) {
                $invitationsReceived->setTo(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Notification[]
     */
    public function getNotifications(): Collection
    {
        return $this->notifications;
    }

    public function addNotification(Notification $notification): self
    {
        if (!$this->notifications->contains($notification)) {
            $this->notifications[] = $notification;
            $notification->setTo($this);
        }

        return $this;
    }

    public function removeNotification(Notification $notification): self
    {
        if ($this->notifications->contains($notification)) {
            $this->notifications->removeElement($notification);
            // set the owning side to null (unless already changed)
            if ($notification->getTo() === $this) {
                $notification->setTo(null);
            }
        }

        return $this;
    }
}
