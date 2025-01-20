<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['product:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Title is required.")]
    #[Groups(['product:read'])]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Photo is required.")]
    #[Groups(['product:read'])]
    private ?string $photo_path = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: "Description is required.")]
    #[Groups(['product:read'])]
    private ?string $description = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['product:read'])]
    private ?string $product_url = null; // Новая необязательная колонка.

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getPhotoPath(): ?string
    {
        return $this->photo_path;
    }

    public function setPhotoPath(string $photo_path): static
    {
        $this->photo_path = $photo_path;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getProductUrl(): ?string
    {
        return $this->product_url;
    }

    public function setProductUrl(?string $url): static
    {
        $this->product_url = $url;

        return $this;
    }

    public function getObject()
    {
        return [
            'id'=>$this->getId(),
            'title'=>$this->getTitle(),
            'photo_path'=>$this->getPhotoPath(),
            'description'=>$this->getDescription(),
            'url'=>$this->getProductUrl(),
        ];

    }
}
