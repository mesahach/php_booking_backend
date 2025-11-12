<?php
declare(strict_types=1);

namespace MyApp\Entity;

use RedBeanPHP\SimpleModel;
use DateTimeImmutable;

class AdminModelEntity extends SimpleModel
{
    private int $id;
    private string $userUuid;
    private ?string $firstname = null;
    private ?string $lastname = null;
    private ?string $phone = null;
    private ?string $email = null;
    private ?string $image = null;
    private ?string $password = null;
    private ?string $status = null;
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

    public function setUserUuid(string $userUuid): self
    {
        $this->userUuid = $userUuid;
        return $this;
    }

    public function getUserUuid(): string
    {
        return $this->userUuid;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;
        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;
        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;
        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
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

    public function setStatus(string $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function hashPassword(string $password): self
    {
        $this->password = password_hash($password, PASSWORD_ARGON2I, ['cost' => 12]);
        return $this;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setCreatedAt(string $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function setUpdatedAt(string $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public function getCreatedAt(): ?string
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?string
    {
        return $this->updatedAt;
    }

    public function __fromArray(?array $data)
    {
        if (!empty($data['id'])) {
            $this->id = (int) $data['id'];
        }
        if (!empty($data['user_uuid'])) {
            $this->userUuid = $data['user_uuid'];
        }
        if (!empty($data['firstname'])) {
            $this->firstname = $data['firstname'];
        }
        if (!empty($data['lastname'])) {
            $this->lastname = $data['lastname'];
        }
        if (!empty($data['emailAddress'])) {
            $this->email = $data['emailAddress'];
        }
        if (!empty($data['email'])) {
            $this->email = $data['email'];
        }
        if (!empty($data['password'])) {
            $this->password = $data['password'];
        }
        if (!empty($data['created_at'])) {
            $this->createdAt = $data['created_at'];
        }
        if (!empty($data['updated_at'])) {
            $this->updatedAt = $data['updated_at'];
        }
        if (!empty($data['createdAt'])) {
            $this->createdAt = $data['createdAt'];
        }
        if (!empty($data['updatedAt'])) {
            $this->updatedAt = $data['updatedAt'];
        }
        if (!empty($data['image'])) {
            $this->image = "https://www." . $_ENV['SERVER_DOMAIN'] . "/images/getProfileImage?file=" . $data['image'];
        }
        if (!empty($data['status'])) {
            $this->status = $data['status'];
        }
        return $this;
    }

    public function __toArray(): array
    {
        // get_object_vars($this); // this will return all the properties of the object
        return [
            'firstname' => $this->firstname,
            'lastname' => $this->lastname,
            'emailAddress' => $this->email,
            'image' => $this->image,
            'status' => $this->status,
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt,
        ];
    }
}