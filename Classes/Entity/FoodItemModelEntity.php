<?php 
declare(strict_types=1);

namespace MyApp\Entity;

use RedBeanPHP\SimpleModel;

class FoodItemModelEntity extends SimpleModel
{
    private int $id;
    private string $uuid;
    private ?string $name = null;
    private ?float $price = null;
    private ?string $description = null;
    private ?string $image = null;
    private ?string $createdAt = null;
    private ?string $updatedAt = null;

    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setUuid(string $uuid): self
    {
        $this->uuid = $uuid;
        return $this;
    }

    public function getUuid(): string
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

    public function setPrice(float $price): self
    {
        $this->price = $price;
        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
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

    public function setImage(string $image): self
    {
        $this->image = $image;
        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
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

    public function __serialize(): array
    {
        return [
            // 'uuid' => $this->uuid,
            'name' => $this->name,
            'price' => $this->price,
            'description' => $this->description,
            'image' => $this->image,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }

    public function __unserialize(?array $data): self
    {
        if (!empty($data['id'])) {
            $this->id = $data['id'];
        }
        if (!empty($data['uuid'])) {
            $this->uuid = $data['uuid'];
        }
        if (!empty($data['name'])) {
            $this->name = $data['name'];
        }
        if (!empty($data['price'])) {
            $this->price = $data['price'];
        }
        if (!empty($data['description'])) {
            $this->description = $data['description'];
        }
        if (!empty($data['image'])) {
            $this->image = $data['image'];
        }
        if (!empty($data['created_at'])) {
            $this->createdAt = $data['created_at'];
        }
        if (!empty($data['updated_at'])) {
            $this->updatedAt = $data['updated_at'];
        }
        return $this;
    }
}