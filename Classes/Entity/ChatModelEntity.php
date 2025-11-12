<?php
namespace MyApp\Entity;

class ChatModelEntity
{
    private ?string $uuid;
    private ?string $senderUuid;
    private ?string $status;
    private ?string $message;
    private ?string $sender;
    private ?string $createdAt;
    private ?string $updatedAt;

    public function setUuid(string $uuid): self
    {
        $this->uuid = $uuid;
        return $this;
    }

    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    public function setSenderUuid(string $senderUuid): self
    {
        $this->senderUuid = $senderUuid;
        return $this;
    }

    public function getSenderUuid(): ?string
    {
        return $this->senderUuid;
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

    public function setMessage(string $message): self
    {
        $this->message = $message;
        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setSender(string $sender): self
    {
        $this->sender = $sender;
        return $this;
    }

    public function getSender(): ?string
    {
        return $this->sender;
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
            'senderUuid' => $this->senderUuid,
            'status' => $this->status,
            'message' => $this->message,
            'sender' => $this->sender,
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt,
        ];
    }

    public function __fromArray(array $data): self
    {
        $this->uuid = $data['uuid'];
        $this->senderUuid = $data['senderUuid'];
        $this->status = $data['status'];
        $this->message = $data['message'];
        $this->sender = $data['sender'];
        $this->createdAt = $data['createdAt'];
        $this->updatedAt = $data['updatedAt'];
        return $this;
    }
}
