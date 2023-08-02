<?php

namespace App\Entity;

use Ramsey\Uuid\Uuid;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\CommentRepository;

#[ORM\Entity(repositoryClass: CommentRepository::class)]
class Comment
{
    const COMMENT_ADDED_SUCCESSFULLY = 'COMMENT_ADDED_SUCCESSFULLY';
    const COMMENT_ERROR = 'COMMENT_ERROR';

    const COMMENT_DELETED_SUCCESSFULLY = 'COMMENT_DELETED_SUCCESSFULLY';


    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type:"uuid", unique:true)]
    private $uuid;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $text = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateMess = null;

    #[ORM\ManyToOne(inversedBy: 'comments')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'comments')]
    #[ORM\JoinColumn(nullable: false, onDelete:'CASCADE')]
    private ?Song $song = null;

    public function __construct()
    {
        $this->uuid = Uuid::uuid4();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function getDateMess(): ?\DateTimeInterface
    {
        return $this->dateMess;
    }

    public function setDateMess(\DateTimeInterface $dateMess): self
    {
        $this->dateMess = $dateMess;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getSong(): ?Song
    {
        return $this->song;
    }

    public function setSong(?Song $song): self
    {
        $this->song = $song;

        return $this;
    }

    // public function __toString(): string
    // {
    //     return $this->text. " " . $this->dateMess;
    // }

    /**
     * Get the value of uuid
     */ 
    public function getUuid()
    {
        return $this->uuid;
    }

    /**
     * Set the value of uuid
     *
     * @return  self
     */ 
    public function setUuid($uuid)
    {
        $this->uuid = $uuid;

        return $this;
    }
}
