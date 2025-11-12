<?php 
declare(strict_types=1);
namespace MyApp\Entity;

use RedBeanPHP\SimpleModel;

final class OtpAuthenticationModelEntity extends SimpleModel
{
    private string $uuid;
    private ?string $userUuid = null;
    private ?string $otpCode = null;
    private ?string $expiresAt = null;
    private ?string $createdAt = null;

    public function setUuid(string $uuid): self
    {
        $this->uuid = $uuid;
        return $this;
    }

    public function setUserUuid(string $userUuid): self
    {
        $this->userUuid = $userUuid;
        return $this;
    }

    public function getUserUuid(): ?string
    {
        return $this->userUuid;
    }

    public function setOtpCode(string $otpCode): self
    {
        $this->otpCode = $otpCode;
        return $this;
    }

    public function getOtpCode(): ?string
    {
        return $this->otpCode;
    }

    public function generateOtp(): self
    {
        $otp = str_pad((string)rand(0, 999999), 6, '0', STR_PAD_LEFT);
        $this->otpCode = $otp;
        return $this;
    }

    public function setExpiresAt(string $expiresAt): self
    {
        $this->expiresAt = $expiresAt;
        return $this;
    }

    public function getExpiresAt(): ?string
    {
        return $this->expiresAt;
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

    public function __serialize(): array
    {
        return [
            // 'userUuid' => $this->userUuid,
            'otpCode' => $this->otpCode,
            'expiresAt' => $this->expiresAt,
            'createdAt' => $this->createdAt,
        ];
    }

    public function __unserialize(?array $data)
    {
        if (!empty($data['user_uuid'])) {
            $this->userUuid = $data['user_uuid'];
        }
        if (!empty($data['otp_code'])) {
            $this->otpCode = $data['otp_code'];
        }
        if (!empty($data['expires_at'])) {
            $this->expiresAt = $data['expires_at'];
        }
        if (!empty($data['created_at'])) {
            $this->createdAt = $data['created_at'];
        }
        return $this;
    }
}
