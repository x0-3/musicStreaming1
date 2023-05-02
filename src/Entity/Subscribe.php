<?php

namespace App\Entity;

use App\Repository\SubscribeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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

    #[ORM\ManyToOne(inversedBy: 'subscribes')]
    private ?User $subscribers = null;

    #[ORM\ManyToOne(inversedBy: 'subscriptions')]
    private ?User $userSubscribes = null;



    public function __construct()
    {
    }

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

    public function getSubscribers(): ?User
    {
        return $this->subscribers;
    }

    public function setSubscribers(?User $subscribers): self
    {
        $this->subscribers = $subscribers;

        return $this;
    }

    public function getUserSubscribes(): ?User
    {
        return $this->userSubscribes;
    }

    public function setUserSubscribes(?User $userSubscribes): self
    {
        $this->userSubscribes = $userSubscribes;

        return $this;
    }

    

}
