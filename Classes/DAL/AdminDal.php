<?php
declare(strict_types=1);

namespace MyApp\DAL;

use RedBeanPHP\R;
use RedBeanPHP\OODBBean;
use RedBeanPHP\SimpleModel;
use MyApp\Entity\AdminModelEntity;
use \RedBeanPHP\RedException\SQL as SQLException;
use PH7\JustHttp\StatusCode;
//
final class AdminDal extends SimpleModel
{
    private const TABLE_NAME = 'admins';


    public function create(AdminModelEntity $data): int|string|false
    {
        $admin = R::dispense(self::TABLE_NAME);
        $admin->user_uuid = $data->getUserUuid();
        $admin->firstname = $data->getFirstName();
        $admin->lastname = $data->getLastname();
        $admin->email = $data->getEmail();
        $admin->image = $data->getImage();
        $admin->password = $data->getPassword();
        $admin->updated_at = $data->getUpdatedAt();
        try {
            $id = R::store($admin);
            // Ensure unique index on email
            R::exec('ALTER TABLE ' . self::TABLE_NAME . ' ADD UNIQUE (email)');
            R::exec('ALTER TABLE ' . self::TABLE_NAME . ' ADD UNIQUE (user_uuid)');
        } catch (SQLException $e) {
            return false;
        } finally {
            R::close();
        }
        return $id;
    }
    
    public static function get(int|string $userUuid): ?AdminModelEntity
    {
        try {
            $admin = is_numeric($userUuid) ? R::load(self::TABLE_NAME, $userUuid) : R::findOne(self::TABLE_NAME, 'user_uuid = :user_uuid', ['user_uuid' => $userUuid]);

            return $admin ? (new AdminModelEntity())->__fromArray($admin->export()) : null;
        } catch (SQLException $e) {
            return null;
        } finally {
            R::close();
        }
    }
    public static function getByEmail(string $email): AdminModelEntity|null
    {
        try {
            $admin = R::findOne(self::TABLE_NAME, 'email = :email', ['email' => $email]);
            return $admin ? (new AdminModelEntity())->__fromArray($admin->export()) : null;
        } catch (SQLException $e) {
            return null;
        } finally {
            R::close();
        }
    }

    public static function isUserExist(string $email): bool
    {
        try {
            $admin = R::findOne(self::TABLE_NAME, 'email = :email', ['email' => $email]);
            return $admin ? true : false;
        } catch (SQLException $e) {
            return false;
        } finally {
            R::close();
        }
    }

    public static function update(int|string $userUuid, AdminModelEntity $data): bool
    {
        $user = R::findOne(self::TABLE_NAME, 'user_uuid = :user_uuid', ['user_uuid' => $userUuid]);

        $firstname = $data->getFirstName();
        if ($firstname) {
            $user->firstname = $firstname;
        }
        $lastname = $data->getLastname();
        if ($lastname) {
            $user->lastname = $lastname;
        }
        $image = $data->getImage();
        if ($image) {
            $user->image = $image;
        }
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
}