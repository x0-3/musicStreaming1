<?php

namespace App\Entity;

use Ramsey\Uuid\Uuid;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\GenreRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Entity(repositoryClass: GenreRepository::class)]
class Genre
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type:"uuid", unique:true)]
    private $uuid;

    #[ORM\Column(length: 255)]
    private ?string $genreName = null;

    #[ORM\OneToMany(mappedBy: 'genre', targetEntity: Song::class)]
    private Collection $songs;

    public function __construct()
    {
        $this->songs = new ArrayCollection();
        $this->uuid = Uuid::uuid4();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGenreName(): ?string
    {
        return $this->genreName;
    }

    public function setGenreName(string $genreName): self
    {
        $this->genreName = $genreName;

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
            $song->setGenre($this);
        }

        return $this;
    }

    public function removeSong(Song $song): self
    {
        if ($this->songs->removeElement($song)) {
            // set the owning side to null (unless already changed)
            if ($song->getGenre() === $this) {
                $song->setGenre(null);
            }
        }

        return $this;
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
