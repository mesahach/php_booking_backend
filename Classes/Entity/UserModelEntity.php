<?php
declare(strict_types=1);

namespace MyApp\Entity;

use RedBeanPHP\SimpleModel;
use DateTimeImmutable;

class UserModelEntity extends SimpleModel
{
    private int $id;
    private string $userUuid;
    private ?string $firstname = null;
    private ?string $lastname = null;
    private ?string $phone = null;
    private ?string $email = null;
    private ?string $image = null;
    private ?string $password = null;
    private ?string $password_text = null;
    private ?string $resident_country = null;
    private ?int $emailAuth = null;
    private ?string $resident_state = null;
    private ?string $address = null;
    private ?string $country = null;
    private ?string $gender = null;
    private ?DateTimeImmutable $date_of_birth = null;
    private ?string $employment_status = null;
    private ?string $account_type = null;
    private ?string $currency = null;
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

    public function setImage(string $image): self 
    {
        $this->image = $image;
        return $this;
    }

    public function setEmailAuth(mixed $emailAuth): self
    {
        $this->emailAuth = (int) $emailAuth;
        return $this;
    }

    public function getEmailAuth(): ?bool
    {
        return $this->emailAuth == '1' || $this->emailAuth == 1 ? true : false;
    }

    public function setPassword(?string $password): self
    {
        $this->password = $password;
        return $this;
    }

    public function setPasswordText(string $password): self
    {
        $this->password_text = $password;
        return $this;
    }

    public function getPasswordText(): ?string
    {
        return $this->password_text;
    }

    public function setResidentCountry(string $country): self
    {
        $this->resident_country = $country;
        return $this;
    }

    public function getResidentCountry(): ?string
    {
        return $this->resident_country;
    }

    public function setResidentState(string $state): self
    {
        $this->resident_state = $state;
        return $this;
    }

    public function getResidentState(): ?string
    {
        return $this->resident_state;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;
        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setCountry(string $country): self
    {
        $this->country = $country;
        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setGender(string $gender): self
    {
        $this->gender = $gender;
        return $this;
    }

    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function setDateOfBirth(DateTimeImmutable $dob): self
    {
        $this->date_of_birth = $dob;
        return $this;
    }

    public function getDateOfBirth(): ?DateTimeImmutable
    {
        return $this->date_of_birth;
    }

    public function setEmploymentStatus(string $status): self
    {
        $this->employment_status = $status;
        return $this;
    }

    public function getEmploymentStatus(): ?string
    {
        return $this->employment_status;
    }

    public function setAccountType(string $type): self
    {
        $this->account_type = $type;
        return $this;
    }

    public function getAccountType(): ?string
    {
        return $this->account_type;
    }

    public function setCurrency(string $currency): self
    {
        $this->currency = $currency;
        return $this;
    }

    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    public function hashPassword(string $password): self
    {
        $this->password = password_hash($password, PASSWORD_ARGON2I, ['cost' => 12]);
        return $this;
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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function getPassword(): ?string
    {

        return $this->password;
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
        if (!empty($data['domain'])) {
            $this->domain = $data['domain'];
        }
        if (!empty($data['emailAddress'])) {
            $this->email = $data['emailAddress'];
        }
        if (!empty($data['email'])) {
            $this->email = $data['email'];
        }
        if (!empty($data['email_auth'])) {
            $this->emailAuth = (int) $data['email_auth'];
        }
        if (!empty($data['emailAuth'])) {
            $this->emailAuth = (int) $data['emailAuth'];
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
        if (isset($data['phone']))
            $this->phone = $data['phone'];
        if (isset($data['date_of_birth']))
            $this->date_of_birth = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $data['date_of_birth'])
                ?: null;
        if (isset($data['gender']))
            $this->gender = $data['gender'];
        if (isset($data['address']))
            $this->address = $data['address'];
        if (isset($data['country']))
            $this->country = $data['country'];
        if (isset($data['resident_country']))
            $this->resident_country = $data['resident_country'];
        if (isset($data['resident_state']))
            $this->resident_state = $data['resident_state'];
        if (isset($data['employment_status']))
            $this->employment_status = $data['employment_status'];
        if (isset($data['account_type']))
            $this->account_type = $data['account_type'];
        if (isset($data['currency']))
            $this->currency = $data['currency'];
        return $this;
    }

    public function __toArray(): array
    {
        // get_object_vars($this); // this will return all the properties of the object
        return [
            'firstname' => $this->firstname,
            'lastname' => $this->lastname,
            'emailAddress' => $this->email,
            'emailAuth' => $this->emailAuth,
            'image' => $this->image,
            'phone' => $this->phone,
            'date_of_birth' => $this->date_of_birth?->format('Y-m-d') ?? null,
            'gender' => $this->gender,
            'address' => $this->address,
            'country' => $this->country,
            // 'resident_country' => $this->resident_country,
            // 'resident_state' => $this->resident_state,
            // 'employment_status' => $this->employment_status,
            // 'account_type' => $this->account_type,
            // 'currency' => $this->currency,
            // 'createdAt' => $this->createdAt,
            // 'updatedAt' => $this->updatedAt,
        ];
    }

}