<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;
    #[ORM\Column(length: 255)]
    private ?string $firstName = null;

    #[ORM\Column(length: 255)]
    private ?string $lastName = null;
    /**
     * @var Collection<int, Reclamation>
     */

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $profilePicture = null;

    public function getProfilePicture(): ?string
    {
        return $this->profilePicture;
    }

    public function setProfilePicture(?string $profilePicture): self
    {
        $this->profilePicture = $profilePicture;
        return $this;
    }

    #[ORM\OneToMany(targetEntity: Reclamation::class, mappedBy: 'user')]
    private Collection $reclamation;

    /**
     * @var Collection<int, Notification>
     */
    #[ORM\OneToMany(targetEntity: Notification::class, mappedBy: 'user')]
    private Collection $notification;

    /**
     * @var Collection<int, Immobilier>
     */
    #[ORM\OneToMany(targetEntity: Immobilier::class, mappedBy: 'user')]
    private Collection $immobilier;


    /**
     * @var Collection<int, Reservation>
     */
    #[ORM\OneToMany(targetEntity: Reservation::class, mappedBy: 'user')]
    private Collection $reservation;

    /**
     * @var Collection<int, Message>
     */
    #[ORM\OneToMany(targetEntity: Message::class, mappedBy: 'sender')]
    private Collection $sentmessages;

    /**
     * @var Collection<int, Message>
     */
    #[ORM\OneToMany(targetEntity: Message::class, mappedBy: 'reciver')]
    private Collection $recivedmessages;

    /**
     * @var Collection<int, Avis>
     */
    #[ORM\OneToMany(targetEntity: Avis::class, mappedBy: 'user')]
    private Collection $avis;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }
    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;
        return $this;
    }

    // Getter et setter pour le nom (lastName)
    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;
        return $this;
    }
    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }
    public function __construct()
    {
        $this->reclamation = new ArrayCollection();
        $this->notification = new ArrayCollection();
        $this->immobilier = new ArrayCollection();
        $this->reservation = new ArrayCollection();
        $this->sentmessages = new ArrayCollection();
        $this->recivedmessages = new ArrayCollection();
        $this->avis = new ArrayCollection();

    }

    /**
     * @return Collection<int, Reclamation>
     */
    public function getReclamation(): Collection
    {
        return $this->reclamation;
    }

    public function addReclamation(Reclamation $reclamation): static
    {
        if (!$this->reclamation->contains($reclamation)) {
            $this->reclamation->add($reclamation);
            $reclamation->setUser($this);
        }

        return $this;
    }

    public function removeReclamation(Reclamation $reclamation): static
    {
        if ($this->reclamation->removeElement($reclamation)) {
            // set the owning side to null (unless already changed)
            if ($reclamation->getUser() === $this) {
                $reclamation->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Notification>
     */
    public function getNotification(): Collection
    {
        return $this->notification;
    }

    public function addNotification(Notification $notification): static
    {
        if (!$this->notification->contains($notification)) {
            $this->notification->add($notification);
            $notification->setUser($this);
        }

        return $this;
    }

    public function removeNotification(Notification $notification): static
    {
        if ($this->notification->removeElement($notification)) {
            // set the owning side to null (unless already changed)
            if ($notification->getUser() === $this) {
                $notification->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Immobilier>
     */
    public function getImmobilier(): Collection
    {
        return $this->immobilier;
    }

    public function addImmobilier(Immobilier $immobilier): static
    {
        if (!$this->immobilier->contains($immobilier)) {
            $this->immobilier->add($immobilier);
            $immobilier->setUser($this);
        }

        return $this;
    }

    public function removeImmobilier(Immobilier $immobilier): static
    {
        if ($this->immobilier->removeElement($immobilier)) {
            // set the owning side to null (unless already changed)
            if ($immobilier->getUser() === $this) {
                $immobilier->setUser(null);
            }
        }

        return $this;
    }



    /**
     * @return Collection<int, Reservation>
     */
    public function getReservation(): Collection
    {
        return $this->reservation;
    }

    public function addReservation(Reservation $reservation): static
    {
        if (!$this->reservation->contains($reservation)) {
            $this->reservation->add($reservation);
            $reservation->setUser($this);
        }

        return $this;
    }

    public function removeReservation(Reservation $reservation): static
    {
        if ($this->reservation->removeElement($reservation)) {
            // set the owning side to null (unless already changed)
            if ($reservation->getUser() === $this) {
                $reservation->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Message>
     */
    public function getSentmessages(): Collection
    {
        return $this->sentmessages;
    }

    public function addSentmessage(Message $sentmessage): static
    {
        if (!$this->sentmessages->contains($sentmessage)) {
            $this->sentmessages->add($sentmessage);
            $sentmessage->setSender($this);
        }

        return $this;
    }

    public function removeSentmessage(Message $sentmessage): static
    {
        if ($this->sentmessages->removeElement($sentmessage)) {
            // set the owning side to null (unless already changed)
            if ($sentmessage->getSender() === $this) {
                $sentmessage->setSender(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Message>
     */
    public function getRecivedmessages(): Collection
    {
        return $this->recivedmessages;
    }

    public function addRecivedmessage(Message $recivedmessage): static
    {
        if (!$this->recivedmessages->contains($recivedmessage)) {
            $this->recivedmessages->add($recivedmessage);
            $recivedmessage->setReciver($this);
        }

        return $this;
    }

    public function removeRecivedmessage(Message $recivedmessage): static
    {
        if ($this->recivedmessages->removeElement($recivedmessage)) {
            // set the owning side to null (unless already changed)
            if ($recivedmessage->getReciver() === $this) {
                $recivedmessage->setReciver(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Avis>
     */
    public function getAvis(): Collection
    {
        return $this->avis;
    }

    public function addAvi(Avis $avi): static
    {
        if (!$this->avis->contains($avi)) {
            $this->avis->add($avi);
            $avi->setUser($this);
        }

        return $this;
    }

    public function removeAvi(Avis $avi): static
    {
        if ($this->avis->removeElement($avi)) {
            // set the owning side to null (unless already changed)
            if ($avi->getUser() === $this) {
                $avi->setUser(null);
            }
        }

        return $this;
    }



}
