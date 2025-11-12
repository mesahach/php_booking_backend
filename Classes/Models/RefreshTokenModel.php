<?php
namespace MyApp\Models;

use MyApp\DAL\RefreshTokenDal;

class RefreshTokenModel
{
    public static function store(string $userUuid, string $jti, int $exp): bool
    {
        return RefreshTokenDal::store($userUuid, $jti, $exp);
    }

    public static function exists(string $userUuid, string $jti): bool
    {
        return RefreshTokenDal::exists($userUuid, $jti);
    }

    public static function revoke(string $jti): bool
    {
        return RefreshTokenDal::revoke($jti);
    }

    public static function revokeAll(string $userUuid): int
    {
        return RefreshTokenDal::revokeAllForUser($userUuid);
    }
}
