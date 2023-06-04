<?php

namespace App\Entity;


use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Post;
use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\HasLifecycleCallbacks()]
#[ApiFilter(SearchFilter::class, properties: ['id' => 'partial', 'userCreationDate' => 'ipartial'])]
#[ApiFilter(OrderFilter::class, properties: ['id', 'userCreationDate'])]
#[ApiResource(
    normalizationContext: ['groups' => ['read']],
    denormalizationContext: ['groups' => ['write']],
    operations: [
        new Get(),
        new GetCollection(paginationItemsPerPage: 2,
    paginationMaximumItemsPerPage: 5),
        new Put(),
        new Patch(),
        new Delete(),
        new Post()
    ],
    
)]

class User 
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups('read')]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['read', 'write'])]
    private ?string $name = null;

    #[ORM\OneToMany(targetEntity:"App\Entity\Image", mappedBy: "user")]
    #[Groups('read')]
    private ?Collection $images = null;

    #[ORM\Column(nullable: true)]
    #[Groups('read')]
    private ?\DateTimeImmutable $userCreationDate = null;

    #[ORM\Column(nullable: true)]
    #[Groups('read')]
    private ?\DateTimeImmutable $userUpdatingDate = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getUserCreationDate(): ?\DateTimeInterface
    {
        return $this->userCreationDate;
    }

    #[ORM\PrePersist]
    public function onCreate(): void
    {
        $this->userCreationDate = new \DateTimeImmutable();
    }

    public function getUserUpdatingDate(): ?\DateTimeInterface
    {
        return $this->userUpdatingDate;
    }

    #[ORM\PreUpdate]
    public function onUpdate(): void
    {
        $this->userUpdatingDate = new \DateTimeImmutable();
    }
}

