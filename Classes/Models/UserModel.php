<?php
namespace MyApp\Models;

use Ramsey\Uuid\Uuid;
use MyApp\Entity\UserModelEntity;
use MyApp\DAL\UserDal;
use PH7\JustHttp\StatusCode;
use Exception;
use PH7\PhpHttpResponseHeader\Http;
use MyApp\Validation\Exception\InvalidUserException;
use DateTimeImmutable;

class UserModel
{
    public const DATE_TIME_FORMAT = 'Y-m-d H:i:s';
    private readonly string $userUuid;

    public function create(array $data)
    {
        $userUuid = Uuid::uuid4();
        $userModel = new UserModelEntity();
        $userModel->setUserUuid($userUuid)
            ->setFirstname($data['firstname'])
            ->setLastname($data['lastname'])
            ->setEmail($data['email'])
            ->setEmailAuth(0)
            ->setStatus("Active")
            ->setPhone($data['phone'])
            ->setPasswordText($data['password'])
            ->hashPassword($data['password'])
            ->setCreatedAt(date(self::DATE_TIME_FORMAT))
            ->setUpdatedAt(date(self::DATE_TIME_FORMAT));

        try {
            $userDal = new UserDal();
            $userDal->create($userModel);
            return ['status' => true, 'message' => 'User created', 'user' => $userModel->__toArray(), 'code' => StatusCode::CREATED];
        } catch (Exception $e) {
            return ['status' => false, 'message' => $e->getMessage(), 'code' => StatusCode::EXPECTATION_FAILED];
        }
    }

    public function get(int|string $id): ?UserModelEntity
    {
        try {
            $user = UserDal::get($id);
            if (!$user) {
                return null;
            }

            return $user;
        } catch (Exception $e) {
            return null;
        }
    }

    public function checkIfIsFirstTime(string $domain): bool
    {
        try {
            $user = UserDal::checkIfDomainIsRegistered($domain);
            return $user;
        } catch (\Throwable $e) {
            return false;
        }
    }

    public function getId(): string
    {
        return $this->userUuid;
    }

    public function isUserExist(string $email): bool
    {
        return UserDal::isUserExist($email);
    }

    public function getAll(): array
    {
        $users = UserDal::getAll();
        $users = array_map(function ($user) {
            $user->setPassword(null);
            $user->setId(null);
            return $user;
        }, $users);
        return $users;
    }

    public function update(int|string $id, array $data): array
    {
        try {
            $user = new UserModelEntity();
            $user->setUserUuid($id);
            if (isset($data['firstname'])) {
                $user->setFirstname($data['firstname']);
            }
            if (isset($data['email_auth'])) {
                $user->setEmailAuth((int) $data['email_auth']);
            }
            if (isset($data['phone'])) {
                $user->setPhone($data['phone']);
            }
            if (isset($data['image'])) {
                $user->setImage($data['image']);
            }
            if (isset($data['gender'])) {
                $user->setGender($data['gender']);
            }
            if (isset($data['date_of_birth'])) {
                $user->setDateOfBirth(new DateTimeImmutable($data['date_of_birth']));
            }
            if (isset($data['address'])) {
                $user->setAddress($data['address']);
            }
            if (isset($data['country'])) {
                $user->setCountry($data['country']);
            }
            $user->setUpdatedAt(date(self::DATE_TIME_FORMAT));

            if (UserDal::update($id, $user)) {
                unset($data['id']);
                return ['status' => true, 'message' => 'User updated', 'user' => $data, 'code' => StatusCode::OK];
            }
            return ['status' => false, 'message' => 'Count not update user', 'code' => StatusCode::EXPECTATION_FAILED];
        } catch (Exception $e) {
            return ['status' => false, 'message' => $e->getMessage(), 'code' => StatusCode::EXPECTATION_FAILED];
        }
    }

    public function updatePassword(int|string $id, array $data): array
    {
        try {
            $user = new UserModelEntity();
            $user->setUserUuid($id);
            $user->setPasswordText($data['password']);
            $user->setPassword($data['password']);
            $user->setUpdatedAt(date(self::DATE_TIME_FORMAT));
            if (UserDal::updatePassword($id, $user)) {
                return ['status' => true, 'message' => 'User password updated', 'code' => StatusCode::OK];
            }
            return ['status' => false, 'message' => 'Count not update user password', 'code' => StatusCode::EXPECTATION_FAILED];
        } catch (Exception $e) {
            return ['status' => false, 'message' => $e->getMessage(), 'code' => StatusCode::EXPECTATION_FAILED];
        }
    }
    public function delete(int|string $id): array
    {
        try {
            $userDal = UserDal::delete($id);
            $code = $userDal ? StatusCode::NO_CONTENT : StatusCode::NOT_FOUND;
            $message = $userDal ? 'User deleted' : 'User not found';
            return ['status' => true, 'message' => $message, 'code' => $code];
        } catch (Exception $e) {
            return ['status' => false, 'message' => $e->getMessage(), 'code' => StatusCode::EXPECTATION_FAILED];
        }
    }

    public function deactivate(): bool
    {
        Http::setHeadersByCode(StatusCode::OK);
        return true;
    }

    public function changePassword(string $uid, string $password): array
    {
        try {
            $user = new UserModelEntity();
            $user->setUserUuid($uid);
            $user->setPasswordText($password);
            $user->hashPassword($password);
            $user->setUpdatedAt(date(self::DATE_TIME_FORMAT));
            $userDal = UserDal::changePassword($user);
            $code = $userDal ? StatusCode::OK : StatusCode::NOT_FOUND;
            $message = $userDal ? 'User password changed' : 'User not found';
            return ['status' => true, 'message' => $message, 'code' => $code];
        } catch (Exception $e) {
            return ['status' => false, 'message' => $e->getMessage(), 'code' => StatusCode::EXPECTATION_FAILED];
        }
    }

    public function getByEmail(string $email): ?UserModelEntity
    {
        try {
            $userDal = UserDal::getByEmail($email);
            if (!$userDal) {
                return null;
            }

            return $userDal;
        } catch (InvalidUserException $e) {
            throw new InvalidUserException("User not found", StatusCode::NOT_FOUND);
            ;
        }
    }
}