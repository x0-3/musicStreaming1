<?php

namespace App\Entity;

use Ramsey\Uuid\Uuid;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
#[UniqueEntity(fields: ['username'], message: 'There is already an account with this username')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type:"uuid", unique:true)]
    private $uuid;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column(nullable:true)]
    private ?string $password = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $avatar = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $username = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Song::class)]
    private Collection $songs;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Comment::class)]
    private Collection $comments;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Playlist::class)]
    private Collection $playlists;

    #[ORM\ManyToMany(targetEntity: Song::class, inversedBy: 'likes')]
    private Collection $likes;

    #[ORM\ManyToMany(targetEntity: Playlist::class, mappedBy: 'userFavorites')]
    private Collection $favoritePlaylists;

    #[ORM\OneToMany(mappedBy: 'user1', targetEntity: Subscribe::class)]
    private Collection $subUser1;

    #[ORM\OneToMany(mappedBy: 'user2', targetEntity: Subscribe::class)]
    private Collection $subUser2;

    #[ORM\Column(type: 'boolean')]
    private $isVerified = false;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Album::class)]
    private Collection $albums;

    #[ORM\Column(type: 'boolean')]
    private ?bool $isBanned = false;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $googleId = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $poster = null;


    public function __construct()
    {
        $this->songs = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->playlists = new ArrayCollection();
        $this->likes = new ArrayCollection();
        $this->favoritePlaylists = new ArrayCollection();
        $this->subUser1 = new ArrayCollection();
        $this->subUser2 = new ArrayCollection();
        $this->albums = new ArrayCollection();
        $this->uuid = Uuid::uuid4();
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
    public function getUserIdentifier(): string
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

    public function isBanned(): ?bool
    {
        return $this->isBanned;
    }

    public function setIsBanned(bool $isBanned): self
    {
        $this->isBanned = $isBanned;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(?string $avatar): self
    {
        $this->avatar = $avatar;
        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

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
            $song->setUser($this);
        }

        return $this;
    }

    public function removeSong(Song $song): self
    {
        if ($this->songs->removeElement($song)) {
            // set the owning side to null (unless already changed)
            if ($song->getUser() === $this) {
                $song->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setUser($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getUser() === $this) {
                $comment->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Playlist>
     */
    public function getPlaylists(): Collection
    {
        return $this->playlists;
    }

    public function addPlaylist(Playlist $playlist): self
    {
        if (!$this->playlists->contains($playlist)) {
            $this->playlists->add($playlist);
            $playlist->setUser($this);
        }

        return $this;
    }

    public function removePlaylist(Playlist $playlist): self
    {
        if ($this->playlists->removeElement($playlist)) {
            // set the owning side to null (unless already changed)
            if ($playlist->getUser() === $this) {
                $playlist->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Song>
     */
    public function getLikes(): Collection
    {
        return $this->likes;
    }

    public function addLike(Song $like): self
    {
        if (!$this->likes->contains($like)) {
            $this->likes->add($like);
        }

        return $this;
    }

    public function removeLike(Song $like): self
    {
        $this->likes->removeElement($like);

        return $this;
    }

    /**
     * @return Collection<int, Playlist>
     */
    public function getFavoritePlaylists(): Collection
    {
        return $this->favoritePlaylists;
    }

    public function addFavoritePlaylist(Playlist $favoritePlaylist): self
    {
        if (!$this->favoritePlaylists->contains($favoritePlaylist)) {
            $this->favoritePlaylists->add($favoritePlaylist);
            $favoritePlaylist->addUserFavorite($this);
        }

        return $this;
    }

    public function removeFavoritePlaylist(Playlist $favoritePlaylist): self
    {
        if ($this->favoritePlaylists->removeElement($favoritePlaylist)) {
            $favoritePlaylist->removeUserFavorite($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Subscribe>
     */
    public function getSubUser1(): Collection
    {
        return $this->subUser1;
    }

    public function addSubUser1(Subscribe $subscribe): self
    {
        if (!$this->subUser1->contains($subscribe)) {
            $this->subUser1->add($subscribe);
            $subscribe->getUser1($this);
        }

        return $this;
    }

    public function removeSubUser1(Subscribe $subUser1): self
    {
        if ($this->subUser1->removeElement($subUser1)) {
            // set the owning side to null (unless already changed)
            if ($subUser1->getUser1() === $this) {
                $subUser1->setUser1(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Subscribe>
     */
    public function getSubUser2(): Collection
    {
        return $this->subUser2;
    }

    public function addSubUser2(Subscribe $subUser2): self
    {
        if (!$this->subUser2->contains($subUser2)) {
            $this->subUser2->add($subUser2);
            $subUser2->setUser2($this);
        }

        return $this;
    }

    public function removeSubUser2(Subscribe $subUser2): self
    {
        if ($this->subUser2->removeElement($subUser2)) {
            // set the owning side to null (unless already changed)
            if ($subUser2->getUser2() === $this) {
                $subUser2->setUser2(null);
            }
        }

        return $this;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    /**
     * @return Collection<int, Album>
     */
    public function getAlbums(): Collection
    {
        return $this->albums;
    }

    public function addAlbum(Album $album): self
    {
        if (!$this->albums->contains($album)) {
            $this->albums->add($album);
            $album->setUser($this);
        }

        return $this;
    }

    public function removeAlbum(Album $album): self
    {
        if ($this->albums->removeElement($album)) {
            // set the owning side to null (unless already changed)
            if ($album->getUser() === $this) {
                $album->setUser(null);
            }
        }

        return $this;
    }

    public function getGoogleId(): ?string
    {
        return $this->googleId;
    }

    public function setGoogleId(?string $googleId): self
    {
        $this->googleId = $googleId;

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

    public function __toString(): string
    {
        return $this->username;
    }

    public function getPoster(): ?string
    {
        return $this->poster;
    }

    public function setPoster(?string $poster): self
    {
        $this->poster = $poster;

        return $this;
    }
}
