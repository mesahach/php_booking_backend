<?php
namespace MyApp\Models;

use Ramsey\Uuid\Uuid;
use MyApp\Entity\AdminModelEntity;
use MyApp\DAL\AdminDal;
use PH7\JustHttp\StatusCode;
use Exception;
use PH7\PhpHttpResponseHeader\Http;
use MyApp\Validation\Exception\InvalidUserException;
use DateTimeImmutable;

class AdminModel
{
    public const DATE_TIME_FORMAT = 'Y-m-d H:i:s';
    private readonly string $userUuid;


    public function getId(): string
    {
        return $this->userUuid;
    }

    public function isUserExist(string $email): bool
    {
        return AdminDal::isUserExist($email);
    }

    public function create(array $data)
    {
        $userUuid = Uuid::uuid4();
        $userModel = new AdminModelEntity();
        $userModel->setUserUuid($userUuid)
            ->setFirstname($data['firstname'])
            ->setLastname($data['lastname'])
            ->setEmail($data['email'])
            ->setStatus("Active")
            ->hashPassword($data['password'])
            ->setCreatedAt(date(self::DATE_TIME_FORMAT))
            ->setUpdatedAt(date(self::DATE_TIME_FORMAT));

        try {
            $userDal = new AdminDal();
            $userDal->create($userModel);
            return ['status' => true, 'message' => 'User created', 'user' => $userModel->__toArray(), 'code' => StatusCode::CREATED];
        } catch (Exception $e) {
            return ['status' => false, 'message' => $e->getMessage(), 'code' => StatusCode::EXPECTATION_FAILED];
        }
    }

    public function get(int|string $id): ?AdminModelEntity
    {
        try {
            $user = AdminDal::get($id); // <-- array or null
            if (!$user) {
                return null;
            }

            return $user;
        } catch (Exception $e) {
            return null;
        }
    }

    public function getByEmail(string $email): ?AdminModelEntity
    {
        try {
            $userDal = AdminDal::getByEmail($email);
            if (!$userDal) {
                return null;
            }

            return $userDal;
        } catch (InvalidUserException $e) {
            throw new InvalidUserException("User not found", StatusCode::NOT_FOUND);
            ;
        }
    }

    public function getAll(): array
    {
        $users = [];//AdminDal::getAll();
        $users = array_map(function ($user) {
            $user = (new AdminModelEntity())->__fromArray($user);
            $password = $user->getPassword();
            $userUuid = $user->getUserUuid();
            $createdAt = $user->getCreatedAt();
            $updatedAt = $user->getUpdatedAt();
            unset($password, $userUuid, $createdAt, $updatedAt);
            return $user->__toArray();
        }, $users);
        return $users;
    }

    public function update(int|string $id, array $data): array
    {
        try {
            $user = new AdminModelEntity();
            $user->setUserUuid($id);
            if (isset($data['firstname'])) {
                $user->setFirstname($data['firstname']);
            }
            if (isset($data['lastname'])) {
                $user->setLastname($data['lastname']);
            }
            if (isset($data['image'])) {
                $user->setImage($data['image']);
            }
            $user->setUpdatedAt(date(self::DATE_TIME_FORMAT));

            if (AdminDal::update($id, $user)) {
                unset($data['id']);
                return ['status' => true, 'message' => 'User updated', 'user' => $data, 'code' => StatusCode::OK];
            }
            return ['status' => false, 'message' => 'Count not update user', 'code' => StatusCode::EXPECTATION_FAILED];
        } catch (Exception $e) {
            return ['status' => false, 'message' => $e->getMessage(), 'code' => StatusCode::EXPECTATION_FAILED];
        }
    }
}