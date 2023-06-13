<?php

namespace App\Entity;

use Ramsey\Uuid\Uuid;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\PlaylistRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Entity(repositoryClass: PlaylistRepository::class)]
class Playlist
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type:"uuid", unique:true)]
    private $uuid;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    #[ORM\Column(length: 255)]
    private ?string $playlistName = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateCreated = null;

    #[ORM\ManyToOne(inversedBy: 'playlists')]	
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?User $user = null;

    #[ORM\ManyToMany(targetEntity: Song::class, inversedBy: 'playlists')]
    private Collection $songs;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'favoritePlaylists')]
    private Collection $userFavorites;


    public function __construct()
    {
        $this->songs = new ArrayCollection();
        $this->userFavorites = new ArrayCollection();
        $this->uuid = Uuid::uuid4();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getPlaylistName(): ?string
    {
        return $this->playlistName;
    }

    public function setPlaylistName(string $playlistName): self
    {
        $this->playlistName = $playlistName;

        return $this;
    }

    public function getDateCreated(): ?\DateTimeInterface
    {
        return $this->dateCreated;
    }

    public function setDateCreated(\DateTimeInterface $dateCreated): self
    {
        $this->dateCreated = $dateCreated;

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

    /**
     * @return Collection<int, Song>
     */
    public function getSongs(): Collection
    {
        return $this->songs;
    }

    public function addSong(Song $song): self
    {
        if (!$this->songs->contains($song)) {
            $this->songs->add($song);
        }

        return $this;
    }

    public function removeSong(Song $song): self
    {
        $this->songs->removeElement($song);

        return $this;
    }

    // TODO: Remove
    // /**
    //  * Update the shuffled order of songs in the playlist
    //  *
    //  * @param Collection|Song[] $shuffledSongs
    //  */
    // public function updateShuffledSongs(Collection $shuffledSongs): void
    // {
    //     $this->songs = $shuffledSongs;
    // }

    /**
     * @return Collection<int, User>
     */
    public function getUserFavorites(): Collection
    {
        return $this->userFavorites;
    }

    public function addUserFavorite(User $userFavorite): self
    {
        if (!$this->userFavorites->contains($userFavorite)) {
            $this->userFavorites->add($userFavorite);
        }

        return $this;
    }

    public function removeUserFavorite(User $userFavorite): self
    {
        $this->userFavorites->removeElement($userFavorite);

        return $this;
    }

    public function isPlaylistByUser(User $user): bool
    {

        return $this->userFavorites->contains($user);

    }

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
