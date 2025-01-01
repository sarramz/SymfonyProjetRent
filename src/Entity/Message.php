<?php

namespace App\Entity;

use App\Repository\MessageRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MessageRepository::class)]
class Message
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $contenu = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date_envoi = null;

    #[ORM\ManyToOne(inversedBy: 'sentmessages')]
    private ?User $sender = null;

    #[ORM\ManyToOne(inversedBy: 'recivedmessages')]
    private ?User $reciver = null;


    public function getId(): ?int
    {
        return $this->id;
    }


    public function getContenu(): ?string
    {

        return $this->contenu;
    }

    public function setContenu(string $contenu): static
    {
        $this->contenu = $contenu;

        return $this;
    }

    public function getDateEnvoi(): ?\DateTimeInterface
    {
        return $this->date_envoi;
    }

    public function setDateEnvoi(\DateTimeInterface $date_envoi): static
    {
        $this->date_envoi = $date_envoi;

        return $this;
    }

    public function getSender(): ?User
    {
        return $this->sender;
    }

    public function setSender(?User $sender): static
    {
        $this->sender = $sender;

        return $this;
    }

    public function getReciver(): ?User
    {
        return $this->reciver;
    }

    public function setReciver(?User $reciver): static
    {
        $this->reciver = $reciver;

        return $this;
    }




}
