<?php
namespace MyApp\Utils;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;
use MyApp\Models\RefreshTokenModel;
use PH7\JustHttp\StatusCode;

class AdminAuthMiddleware
{
    /**
     * Verify access token from Authorization header
     */
    public static function verifyAccessToken(): array
    {
        if (!preg_match('/Bearer\s(\S+)/', $_SERVER['HTTP_AUTHORIZATION'], $matches)) {
            return [
                'status' => false,
                'code' => StatusCode::UNAUTHORIZED,
                'message' => 'Missing or invalid Authorization header',
                'error' => 'no_token'
            ];
        }

        $token = $matches[1];

        try {
            $decoded = JWT::decode(
                $token,
                new Key($_ENV['JWT_SECRET_KEY_ADMIN'], $_ENV['JWT_ALGO_ENC_ADMIN'])
            );

            return [
                'status' => true,
                'code' => StatusCode::OK,
                'user_uuid' => $decoded->data->user_uuid,
                'decoded' => $decoded
            ];

        } catch (ExpiredException $e) {
            return [
                'status' => false,
                'code' => StatusCode::UNAUTHORIZED, // triggers refresh
                'message' => 'Access token expired',
                'error' => 'token_expired'
            ];
        } catch (\Exception $e) {
            return [
                'status' => false,
                'code' => StatusCode::UNAUTHORIZED,
                'message' => 'Invalid access token',
                'error' => 'token_invalid'
            ];
        }
    }

    /**
     * Verify refresh token validity (used by refreshAccessToken)
     */
    public static function verifyRefreshToken(): array
    {
        try {
            $decoded = JWT::decode(
                $_SERVER['HTTP_AUTHORIZATION'],
                new Key($_ENV['JWT_SECRET_KEY_ADMIN'], $_ENV['JWT_ALGO_ENC_ADMIN'])
            );

            // Check DB if refresh token jti still exists
            if (!RefreshTokenModel::exists($decoded->data->user_uuid, $decoded->jti)) {
                return [
                    'status' => false,
                    'code' => StatusCode::UNAUTHORIZED,
                    'message' => 'Invalid or revoked refresh token',
                    'error' => 'refresh_invalid'
                ];
            }

            return [
                'status' => true,
                'code' => StatusCode::OK,
                'user_uuid' => $decoded->data->user_uuid,
                'jti' => $decoded->jti,
                'decoded' => $decoded
            ];

        } catch (ExpiredException $e) {
            RefreshTokenModel::revoke($decoded->jti ?? null); // cleanup if exists
            return [
                'status' => false,
                'code' => StatusCode::FORBIDDEN,
                'message' => 'Refresh token expired. Please login again.',
                'error' => 'refresh_expired'
            ];
        } catch (\Exception $e) {
            RefreshTokenModel::revoke($decoded->jti ?? null); // cleanup if exists
            return [
                'status' => false,
                'code' => StatusCode::FORBIDDEN,
                'message' => 'Invalid refresh token',
                'error' => 'refresh_invalid'
            ];
        }
    }
}
