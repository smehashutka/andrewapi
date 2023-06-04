<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Post;
use App\Repository\ImageRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: ImageRepository::class)]
#[ORM\HasLifecycleCallbacks()]
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
    ]
)]
#[Vich\Uploadable]
#[ApiFilter(SearchFilter::class, properties: ['id' => 'partial', 'imageCreationDate' => 'ipartial'])]
#[ApiFilter(OrderFilter::class, properties: ['id', 'imageCreationDate'])]

class Image
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups('read')]
    private ?int $id = null;

//    #[Groups('read')]
    #[Vich\UploadableField(mapping: 'images', fileNameProperty: 'imageName')]
    private ?File $imageFile = null;

    #[ORM\Column(length: 255)]
    #[Groups(['read', 'write'])]
    private ?string $imageName = null;

    #[ORM\Column(nullable: true)]
    #[Groups('read')]
    private ?\DateTimeImmutable $imageUpdatingDate = null;

    #[ORM\Column(nullable: true)]
    #[Groups('read')]
    private ?\DateTimeImmutable $imageCreationDate = null;

    #[ORM\Column(length: 255)]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups('read')]
    private ?string $imagePath = null;

    #[ORM\ManyToOne(inversedBy: 'images')]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: false)]
    #[Groups('read')]
    private ?User $user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile|null $imageFile
     */
    public function setImageFile(?File $imageFile = null): void
    {
        $this->imageFile = $imageFile;

        if (null !== $imageFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->imageUpdatingDate = new \DateTimeImmutable();
        }
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    public function setImageName(string $imageName): self
    {
        $this->imageName = $imageName;

        return $this;
    }

    public function getImagePath(): ?string
    {
        return $this->imagePath;
    }

    public function setImagePath(string $imagePath): self
    {
        $this->imagePath = $imagePath;

        return $this;
    }

    public function getRelation(): ?User
    {
        return $this->user;
    }

    public function setRelation(?User $relation): self
    {
        $this->user = $relation;

        return $this;
    }

    public function getImageUpdatingDate(): ?\DateTimeImmutable
    {
        return $this->imageUpdatingDate;
    }

    #[ORM\PreUpdate]
    public function onUpdate(): void
    {
        $this->imageUpdatingDate = new \DateTimeImmutable();
    }

    public function getImageCreationDate(): ?\DateTimeImmutable
    {
        return $this->imageCreationDate;
    }

    #[ORM\PrePersist]
    public function onCreate(): void
    {
        $this->imageCreationDate = new \DateTimeImmutable();

    }

}
