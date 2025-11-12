<?php 
namespace MyApp\Entity;

use DateTimeImmutable;
use InvalidArgumentException;

class BookingModelEntity
{
    private ?string $uuid;
    private ?string $user_uuid;
    private ?string $celebrityUuid;
    private ?DateTimeImmutable $bookingDate;
    private ?DateTimeImmutable $bookingTime;
    private ?string $bookingStatus;
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
        $this->user_uuid = $uuid;
        return $this;
    }

    public function getUserUuid(): ?string
    {
            return $this->user_uuid;
    }

    public function setBookingDate(?string $date): self
    {
        $this->bookingDate = $date ? DateTimeImmutable::createFromFormat('Y-m-d', $date) : null;
        return $this;
    }

    public function getBookingDate(): ?DateTimeImmutable
    {
        return $this->bookingDate;
    }

    public function setBookingTime(?string $time): self
    {
        if (!$time) {
            $this->bookingTime = null;
            return $this;
        }

        $time = trim($time);

        // Normalize: remove extra spaces
        $time = preg_replace('/\s+/', ' ', $time);

        // Try common formats in order of strictness
        $formats = [
            'H:i:s',   // 20:00:00
            'H:i',     // 20:00
            'g:i:s A', // 8:00:00 PM
            'g:i:s a', // 8:00:00 pm
            'g:i A',   // 8:00 PM
            'g:i a',   // 8:00 pm
            'g:iA',    // 8:00PM
            'g:ia',    // 8:00pm
            'g A',     // 8 PM
            'g a',     // 8 pm
            'ga',      // 8pm
            'gA',      // 8PM
        ];

        foreach ($formats as $format) {
            $dt = DateTimeImmutable::createFromFormat('!' . $format, $time);
            if ($dt !== false && $dt->format($format) === $time) {
                $this->bookingTime = $dt;
                return $this;
            }
        }

        // Last resort: strtotime (very flexible, but risky)
        $timestamp = strtotime($time);
        if ($timestamp !== false) {
            $dt = (new DateTimeImmutable())->setTimestamp($timestamp);
            // Validate it's a time (not date)
            if (preg_match('/^(?:[01]?[0-9]|2[0-3]):[0-5][0-9](?::[0-5][0-9])?$/', $dt->format('H:i:s'))) {
                $this->bookingTime = $dt;
                return $this;
            }
        }

        throw new InvalidArgumentException(
            "Invalid time: '$time'. Use formats like: 20:00, 8:00 PM, 8pm, 08:00:00 pm"
        );
    }

    public function getBookingTime(): ?DateTimeImmutable
    {
        return $this->bookingTime;
    }

    public function setBookingStatus(string $bookingStatus): self
    {
        $this->bookingStatus = $bookingStatus;
        return $this;
    }

    public function getBookingStatus(): ?string
    {
        return $this->bookingStatus;
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
            'bookingDate' => $this->bookingDate->format('Y-m-d'),
            'bookingTime' => $this->bookingTime->format('H:i'),
            'bookingStatus' => $this->bookingStatus,
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt,
        ];
    }

    public function __fromArray(array $data): self
    {
        $this->uuid = $data['uuid'] ?? null;
        $this->celebrityUuid = $data['celebrity_uuid'] ?? null;
        $this->bookingDate = $data['booking_date']
            ? DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $data['booking_date'])
            : null;
        $this->bookingDate = DateTimeImmutable::createFromFormat('Y-m-d', $this->bookingDate->format('Y-m-d'));

        $this->bookingTime = $data['booking_time']
            ? DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $data['booking_time'])
            ?? DateTimeImmutable::createFromFormat('H:i', $data['booking_time'])
            : null;
        $this->bookingTime = DateTimeImmutable::createFromFormat('H:i', $this->bookingTime->format('H:i'));

        $this->bookingStatus = $data['booking_status'];
        $this->createdAt = $data['created_at'];
        $this->updatedAt = $data['updated_at'];
        return $this;
    }
}