<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;
use App\Repository\ReleaseRepository;
use App\Entity\Artist;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Annotation\Context;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;


#[ORM\Entity(repositoryClass: ReleaseRepository::class)]
#[ApiResource(
    operations: [
        new Get(normalizationContext: ['groups' => 'release:item:read']),
        new GetCollection(normalizationContext: ['groups' => 'release:list:read']),
        new Post(),
        new Put(),
        new Delete()
    ],
    //order: ['year' => 'DESC', 'city' => 'ASC'],
    paginationEnabled: true,
)]
class Release
{
    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['release:list:read', 'artist:item:read', 'release:item:read'])]
    private ?int $id = null;

    
    #[ORM\Column(type:"string", length:255)]
    #[Assert\Choice(choices:["album", "single", "compilation"])]
    #[Groups(['release:list:read', 'artist:item:read', 'release:item:read'])]
    private ?string $type = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Groups(['release:list:read', 'artist:item:read', 'release:item:read'])]
    private ?string $name = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    #[Context([DateTimeNormalizer::FORMAT_KEY => 'Y-m-d'])]
    #[Groups(['release:list:read', 'artist:item:read', 'release:item:read'])]
    private ?\DateTimeInterface $releaseDate = null;

    #[ORM\ManyToOne(targetEntity: Artist::class, inversedBy: 'releases')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['release:item:read'])]
    private $artist;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['release:list:read', 'artist:item:read', 'release:item:read'])]
    private ?string $spotifyId = null;

    #[ORM\Column(type: "integer")]
    #[Range(min: 0, max: 100)]
    #[Groups(['release:list:read', 'artist:item:read', 'release:item:read'])]
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

    public function getReleaseDate(): ?\DateTimeInterface
    {
        return $this->releaseDate;
    }

    public function setReleaseDate(?\DateTimeInterface $releaseDate): self
    {
        $this->releaseDate = $releaseDate;

        return $this;
    }
}
