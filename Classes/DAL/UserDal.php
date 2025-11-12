<?php
declare(strict_types=1);

namespace MyApp\DAL;

use RedBeanPHP\R;
use RedBeanPHP\OODBBean;
use RedBeanPHP\SimpleModel;
use MyApp\Entity\UserModelEntity;
use \RedBeanPHP\RedException\SQL as SQLException;
use PH7\JustHttp\StatusCode;
use DateTimeImmutable;
//
final class UserDal extends SimpleModel
{
    private const TABLE_NAME = 'users';

    public function create(UserModelEntity $data): int|string|false
    {
        $user = R::dispense(self::TABLE_NAME);
        $user->user_uuid = $data->getUserUuid();
        $user->firstname = $data->getFirstName();
        $user->lastname = $data->getLastname();
        $user->phone = $data->getPhone();
        $user->email_auth = $data->getEmailAuth();
        $user->email = $data->getEmail();
        $user->image = $data->getImage();
        $user->password = $data->getPassword();
        $user->gender = $data->getGender();
        $user->date_of_birth = $data->getDateOfBirth();
        $user->address = $data->getAddress();
        $user->country = $data->getCountry();
        $user->status = $data->getStatus();
        $user->password_text = $data->getPasswordText();
        $user->created_at = $data->getCreatedAt();
        $user->updated_at = $data->getUpdatedAt();
        try {
            $id = R::store($user);
            // Ensure unique index on email
            R::exec('ALTER TABLE ' . self::TABLE_NAME . ' ADD UNIQUE (email)');
            R::exec('ALTER TABLE ' . self::TABLE_NAME . ' ADD UNIQUE (phone)');
            R::exec('ALTER TABLE ' . self::TABLE_NAME . ' ADD UNIQUE (user_uuid)');
        } catch (SQLException $e) {
            return false;
        } finally {
            R::close();
        }
        return $id;
    }

    public static function get(int|string $userUuid): ?UserModelEntity
    {
        try {
            $user = is_numeric($userUuid) ? R::load(self::TABLE_NAME, $userUuid) : R::findOne(self::TABLE_NAME, 'user_uuid = :user_uuid', ['user_uuid' => $userUuid]);
            
            return $user ? (new UserModelEntity())->__fromArray($user->export()) : null;
        } catch (SQLException $e) {
            return null;
        } finally {
            R::close();
        }
    }

    public static function checkIfDomainIsRegistered(string $domain): bool
    {
        try {
            $user = R::findOne(self::TABLE_NAME, "domain = :domain", ['domain' => $domain]);
            return (bool) $user?->id;
        } catch (\Throwable $e) {
            return false;
        } finally {
            R::close();
        }
    }

    public static function getAll(): array
    {
        try {
            $users = R::findAll(self::TABLE_NAME);
            $users = array_map(function ($user) {
                unset($user->password, $user->id);
                $user->initials = strtoupper(substr($user->firstname, 0, 1) . substr($user->lastname, 0, 1));
                $user->email_auth = (bool) $user->email_auth;
                $user->image = !empty($user->image) ? "https://www." . $_ENV['SERVER_DOMAIN'] . "/images/getProfileImage?file=" . $user->image : $user->image;
                $created_at = new DateTimeImmutable($user->created_at);
                $user->whenCreated = $created_at->diff(new DateTimeImmutable())->m;
                return $user;
            }, $users);
            return (array)$users;
        } catch (SQLException $e) {
            return [];
        } finally {
            R::close();
        }
    }

    public static function delete(int|string $userUuid): bool
    {
        try {
            $userBean = R::findOne(self::TABLE_NAME, 'user_uuid = :user_uuid', ['user_uuid' => $userUuid]);
            if (!$userBean) {
                return false;
            }
            $id = (bool) R::trash($userBean);
            return $id;
        } catch (SQLException $e) {
            return false;
        } finally {
            R::close();
        }
    }

    public static function update(int|string $userUuid, UserModelEntity $data): bool
    {
        $user = R::findOne(self::TABLE_NAME, 'user_uuid = :user_uuid', ['user_uuid' => $userUuid]);
        $user->user_uuid = $data->getUserUuid();

        $password = $data->getPassword();
        if ($password) {
            $user->password = $password;
        }
        $email = $data->getEmail();
        if ($email) {
            $user->email = $email;
        }
        $image = $data->getImage();
        if ($image) {
            $user->image = $image;
        }
        $firstname = $data->getFirstName();
        if ($firstname) {
            $user->firstname = $firstname;
        }
        $lastname = $data->getLastname();
        if ($lastname) {
            $user->lastname = $lastname;
        }
        $resident_country = $data->getResidentCountry();
        if ($resident_country) {
            $user->resident_country = $resident_country;
        }
        $status = $data->getStatus();
        if ($status) {
            $user->status = $status;
        }
        $emailAuth = $data->getEmailAuth();
        if ($emailAuth) {
            $user->email_auth = (int) $emailAuth;
        }
        $country = $data->getCountry();
        if ($country) {
            $user->country = $country;
        }
        $resident_country = $data->getResidentCountry();
        if ($resident_country) {
            $user->resident_country = $resident_country;
        }
        $date_of_birth = $data->getDateOfBirth();
        if ($date_of_birth) {
            $user->date_of_birth = $date_of_birth;
        }
        if ($data->getGender()) {
            $user->gender = $data->getGender();
        }
        if ($data->getAddress()) {
            $user->address = $data->getAddress();
        }
        
        $user->updated_at = $data->getUpdatedAt();
        try {
            $id = R::store($user);
            R::close();
            return true;
        } catch (SQLException $e) {
            return false;
        } finally {
            R::close();
        }
    }

    public static function updatePassword(int|string $userUuid, UserModelEntity $data): bool 
    {
        try {
            $user = R::findOne(self::TABLE_NAME, 'user_uuid = :user_uuid', ['user_uuid' => $userUuid]);
            $user->password = $data->getPassword();
            $user->password_text = $data->getPasswordText();
            $user->updated_at = $data->getUpdatedAt();
            $id = R::store($user);
            R::close();
            return true;
        } catch (SQLException $e) {
            return false;
        } finally {
            R::close();
        }
    }
    
    public static function getByEmail(string $email): UserModelEntity|null
    {
        try {
            $user = R::findOne(self::TABLE_NAME, 'email = :email', ['email' => $email]);
            return $user ? (new UserModelEntity())->__fromArray($user->export()) : null;
        } catch (SQLException $e) {
            return null;
        } finally {
            R::close();
        }
    }

    public static function getByPhone(string $phone): UserModelEntity|null
    {
        try {
            $user = R::findOne(self::TABLE_NAME, 'phone = :phone', ['phone' => $phone]);
            return $user ? (new UserModelEntity())->__fromArray($user->export()) : null;
        } catch (SQLException $e) {
            return null;
        } finally {
            R::close();
        }
    }

    public static function isUserExist(string $email): bool
    {
        try {
            $user = R::findOne(self::TABLE_NAME, 'email = :email', ['email' => $email]);
            return $user ? true : false;
        } catch (SQLException $e) {
            return false;
        } finally {
            R::close();
        }
    }

    public static function changePassword(UserModelEntity $user): bool
    {
        try {
            $user = R::findOne(self::TABLE_NAME, 'user_uuid = :user_uuid', ['user_uuid' => $user->getUserUuid()]);
            $user->password = $user->getPassword();
            $id = R::store($user);
            R::close();
            return true;
        } catch (SQLException $e) {
            return false;
        } finally {
            R::close();
        }
    }
}