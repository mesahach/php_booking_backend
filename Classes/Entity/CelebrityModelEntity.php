<?php 
namespace MyApp\Entity;

class CelebrityModelEntity
{
    private ?int $id;
    private ?string $uuid;
    private ?string $name;
    private ?string $image;
    private ?string $status;
    private ?string $description;
    private ?string $createdAt;
    private ?string $updatedAt;

    public function setId(?int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setUuid(?string $uuid): self
    {
        $this->uuid = $uuid;
        return $this;
    }

    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setImage(string $image): self
    {
        $this->image = "https://www." . $_ENV['SERVER_DOMAIN'] . "/images/getProfileImage?file=" . $image;
        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setStatus(string $status):self
    {
        $this->status = $status;
        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setCreatedAt(string $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getCreatedAt(): ?string
    {
        return $this->createdAt;
    }

    public function setUpdatedAt(string $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public function getUpdatedAt(): ?string
    {
        return $this->updatedAt;
    }

    public function __toArray(): array
    {
        return [
            'uuid' => $this->uuid,
            'name' => $this->name,
            'image' => $this->image,
            'status' => $this->status,
            'description' => $this->description,
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt,
        ];
    }

    public function __fromArray(array $data): self
    {
        $this->uuid = $data['uuid'];
        $this->name = $data['name'];
        $this->image = $data['image'];
        $this->status = $data['status'];
        $this->description = $data['description'];
        $this->createdAt = $data['created_at']?? $data['createdAt'] ?? '';
        $this->updatedAt = $data['updated_at']?? $data['updatedAt'] ?? '';
        return $this;
    }
}
