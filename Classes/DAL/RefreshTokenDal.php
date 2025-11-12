<?php
namespace MyApp\DAL;

use RedBeanPHP\R;
use RedBeanPHP\OODBBean;
use RedBeanPHP\RedException\SQL;

class RefreshTokenDal
{
    private const TABLE_NAME = 'refreshtokens';

    public static function store(string $userUuid, string $jti, int $exp): bool
    {
        try {
            $bean = R::dispense(self::TABLE_NAME);
            $bean->user_uuid = $userUuid;
            $bean->token_jti = $jti;
            $bean->expires_at = date('Y-m-d H:i:s', $exp);

            R::store($bean);
            return true;
        } catch (SQL $e) {
            return false;
        } finally {
            R::close();
        }
    }

    public static function exists(string $userUuid, string $jti): bool
    {
        try {
            $token = R::findOne(
                self::TABLE_NAME,
                'user_uuid = :user_uuid AND token_jti = :jti AND expires_at > NOW()',
                ['user_uuid' => $userUuid, 'jti' => $jti]
            );
            return $token !== null;
        } catch (SQL $e) {
            return false;
        } finally {
            R::close();
        }
    }

    public static function revoke(string $jti): bool
    {
        try {
            $token = R::findOne(self::TABLE_NAME, 'token_jti = ?', [$jti]);
            if ($token) {
                R::trash($token);
                return true;
            }
            return false;
        } catch (SQL $e) {
            return false;
        } finally {
            R::close();
        }
    }

    public static function revokeAllForUser(string $userUuid): int
    {
        try {
            return R::exec(
                'DELETE FROM ' . self::TABLE_NAME . ' WHERE user_uuid = ?',
                [$userUuid]
            );
        } catch (SQL $e) {
            return 0;
        } finally {
            R::close();
        }
    }
}