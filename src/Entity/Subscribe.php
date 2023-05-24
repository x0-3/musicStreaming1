<?php

namespace App\Entity;

use App\Repository\SubscribeRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SubscribeRepository::class)]
class Subscribe
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateFollow = null;

    #[ORM\ManyToOne(inversedBy: 'subUser1')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?User $user1 = null; // user that is subscribed to an artist

    #[ORM\ManyToOne(inversedBy: 'subUser2')]
    private ?User $user2 = null; // artist that has subscriptions

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateFollow(): ?\DateTimeInterface
    {
        return $this->dateFollow;
    }

    public function setDateFollow(\DateTimeInterface $dateFollow): self
    {
        $this->dateFollow = $dateFollow;

        return $this;
    }

    public function getUser1(): ?User
    {
        return $this->user1;
    }

    public function setUser1(?User $user1): self
    {
        $this->user1 = $user1;

        return $this;
    }


    public function getUser2(): ?User
    {
        return $this->user2;
    }

    public function setUser2(?User $user2): self
    {
        $this->user2 = $user2;

        return $this;
    }


}
