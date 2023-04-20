<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\ReleaseRepository;
use App\Entity\Artist;
use Symfony\Component\Validator\Constraints\Range;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;


#[ORM\Entity(repositoryClass: ReleaseRepository::class)]
#[ApiResource(
    operations: [
        new Get(),
        new GetCollection(),
        new Post(),
        new Put(),
        new Delete()
    ],
    //order: ['year' => 'DESC', 'city' => 'ASC'],
    paginationEnabled: false,
)]
class Release
{
    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    
    #[ORM\Column(type:"string", length:255)]
    #[Assert\Choice(choices:["album", "single", "compilation"])]
    private ?string $type = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?string $name = null;

    #[ORM\ManyToOne(targetEntity: "Artist", inversedBy: "releases")]
    #[ORM\JoinColumn(nullable: false)]
    private $artist;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $spotifyId = null;

    #[ORM\Column(type: "integer")]
    #[Range(min: 0, max: 100)]
    private ?int $popularity = null;
    
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = preg_replace('/\s+/', ' ', trim(ucwords(strtolower($name))));

        return $this;
    }

    public function getArtist(): ?Artist
    {
        return $this->artist;
    }

    public function setArtist(?Artist $artist): self
    {
        $this->artist = $artist;

        return $this;
    }

    // Convenience method to get artist info
    public function getArtistInfo(): ?array
    {
        if ($this->artist) {
            return [
                'id' => $this->artist->getId(),
                'name' => $this->artist->getName(),
                'firstName' => $this->artist->getFirstName(),
                'lastName' => $this->artist->getLastName(),
                'dateOfBirth' => $this->artist->getDateOfBirth(),
                'spotifyId' => $this->artist->getSpotifyId(),
            ];
        }

        return null;
    }

    public function getSpotifyId(): ?string
    {
        return $this->spotifyId;
    }

    public function setSpotifyId(?string $spotifyId): self
    {
        $this->spotifyId = $spotifyId;

        return $this;
    }

    public function getPopularity(): ?int
    {
        return $this->popularity;
    }

    public function setPopularity(?int $popularity): self
    {
        $this->popularity = $popularity;

        return $this;
    }
}
