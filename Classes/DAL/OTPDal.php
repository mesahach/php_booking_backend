<?php 
declare(strict_types=1);

namespace MyApp\DAL;

use RedBeanPHP\R;
use RedBeanPHP\OODBBean;
use RedBeanPHP\SimpleModel;
use MyApp\Entity\OtpAuthenticationModelEntity;
use \RedBeanPHP\RedException\SQL as SQLException;

final class OTPDal extends SimpleModel
{
    private const TABLE_NAME = 'otpauthentications';

    public static function create(OtpAuthenticationModelEntity $data): int|string|null
    {
        $auth = R::dispense(self::TABLE_NAME);
        $auth->user_uuid = $data->getUserUuid();
        $auth->otp_code = $data->getOtpCode();
        $auth->expires_at = $data->getExpiresAt();
        $auth->created_at = $data->getCreatedAt();
        try {
            $id = R::store($auth);
            // Ensure unique index on email
            R::exec('ALTER TABLE ' . self::TABLE_NAME . ' ADD UNIQUE (email)');
            R::exec('ALTER TABLE ' . self::TABLE_NAME . ' ADD UNIQUE (user_uuid)');
            return $id;
        } catch (SQLException $e) {
            return null;
        } finally {
            R::close();
        }
    }

    public static function get(string $userUuid): ?OtpAuthenticationModelEntity
    {
        $records = R::find(
            self::TABLE_NAME,
            'user_uuid = :user_uuid ORDER BY id DESC',
            ['user_uuid' => $userUuid]
        );

        if ($records) {
            // get the first (latest) record
            $auth = reset($records);
            return (new OtpAuthenticationModelEntity())->__unserialize($auth->export());
        }

        return null;
    }

    public static function update(OtpAuthenticationModelEntity $data): int|string|null
    {
        $auth = R::findOne(self::TABLE_NAME, 'user_uuid = ?', [$data->getUserUuid()]);
        if ($auth) {
            $auth->otp_code = $data->getOtpCode();
            $auth->expires_at = $data->getExpiresAt();
            $auth->created_at = $data->getCreatedAt();
            try {
                $id = R::store($auth);
                return $id;
            } catch (SQLException $e) {
                return null;
            } finally {
                R::close();
            }
        }
        return null;
    }

    public static function delete(string $userUuid): int|string|null
    {
        $user = R::findAll(self::TABLE_NAME, 'user_uuid = ?', [$userUuid]);
        if ($user) {
            try {
                $id = R::trashAll($user);
                return $id;
            } catch (SQLException $e) {
                return null;
            } finally {
                R::close();
            }
        }
        return null;
    }
}