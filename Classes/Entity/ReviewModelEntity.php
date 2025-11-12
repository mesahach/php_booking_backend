<?php
namespace MyApp\Entity;

class ReviewModelEntity
{
    private int $id;
    private ?string $uuid;
    private ?string $celebrityUuid;
    private ?string $userUuid;
    private ?string $name;
    private ?string $image;
    private ?string $review;
    private ?string $stars;
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

    public function setCelebrityUuid(string $uuid): self
    {
        $this->celebrityUuid = $uuid;
        return $this;
    }

    public function getCelebrityUuid(): ?string
    {
        return $this->celebrityUuid;
    }

    public function setUserUuid(string $uuid): self
    {
        $this->userUuid = $uuid;
        return $this;
    }

    public function getUserUuid(): ?string
    {
        return $this->userUuid;
    }

    public function setReview(string $review): self
    {
        $this->review = $review;
        return $this;
    }

    public function getReview(): ?string
    {
        return $this->review;
    }

    public function setStars(int $stars): self
    {
        $this->stars = $stars;
        return $this;
    }

    public function getStars(): ?int
    {
        return $this->stars;
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
            'celebrityUuid' => $this->celebrityUuid,
            'userUuid' => $this->userUuid,
            'name' => $this->name,
            'image' => $this->image,
            'review' => $this->review,
            'stars' => $this->stars,
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt,
        ];
    }

    public function __fromArray(array $data): self
    {
        $this->id = $data['id'];
        $this->uuid = $data['uuid'];
        $this->celebrityUuid = $data['celebrityUuid'] ?? $data['celebrity_uuid'] ?? '';
        $this->userUuid = $data['userUuid'] ?? $data['user_uuid'] ?? '';
        $this->name = $data['name'] ?? '';
        $this->image = $data['image'] ?? '';
        $this->review = $data['review'] ?? '';
        $this->stars = $data['stars'] ?? 0;
        $this->createdAt = $data['createdAt'] ?? $data['created_at'] ?? '';
        $this->updatedAt = $data['updatedAt'] ?? $data['updated_at'] ?? '';
        return $this;
    }
}
