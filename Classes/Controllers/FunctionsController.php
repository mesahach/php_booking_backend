<?php

namespace MyApp\Controllers;

use Exception;
use MyApp\Utils\AuthMiddleware;
use MyApp\Utils\ValidatorHelper as v;
use PH7\JustHttp\StatusCode;
use MyApp\Entity\UserModelEntity;
use MyApp\Models\UserModel;
use stdClass;
use Firebase\JWT\ExpiredException; // <-- Need to catch this
use Firebase\JWT\SignatureInvalidException; // <-- Need to catch this
use Firebase\JWT\BeforeValidException; // <-- Need to catch this
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use MyApp\Models\RefreshTokenModel;

/**
 * This class contains common functions used by other controllers.
 */
abstract class FunctionsController
{
    /**
     * Validate required parameters in the input array.
     *
     * @param array $params
     * @param array $requiredKeys
     * @return void
     */
    protected function validateParams(array $params, array $requiredKeys): void
    {
        foreach ($requiredKeys as $key) {
            if (!isset($params[$key])) {
                response([
                    'code' => StatusCode::UNPROCESSABLE_ENTITY,
                    'message' => "Missing required parameter: $key",
                    'status' => false,
                ]);
                exit;
            }
        }
    }

    /**
     * Validate input data using validators.
     *
     * @param array $validators Array of validators.
     * @return void
     */
    public function validateInputs(array $validators): void
    {
        foreach ($validators as $validator) {
            if (!$validator['status']) {
                response([
                    'status' => false,
                    'message' => $validator['message'],
                    'details' => $validator['details']
                ], StatusCode::UNPROCESSABLE_ENTITY);
                exit;
            }
        }
    }


    public function verifyCaptcha($data): bool
    {
        $client = new \GuzzleHttp\Client();
        $keys = $_ENV['CAPTCHA_SECRET_KEY'];

        $url = 'https://www.google.com/recaptcha/api/siteverify';
        $response = $client->post($url, [
            'form_params' => [
                'secret' => $keys,
                'response' => $data['g-recaptcha-response'],
            ]
        ]);

        $responseData = json_decode($response->getBody(), true);

        if ($responseData['success'] == false) {
            return false;
        }
        return true;
    }

    protected function authenticateUser(): array
    {
        $auth = AuthMiddleware::verifyAccessToken();
        if (!$auth['status']) {
            return $auth;
        }

        $validator = v::validateUserUuid($auth['user_uuid']);
        if (!$validator['status']) {
            return $validator;
        }
        return ['status' => true, 'message' => "User is loggedIn", 'code' => StatusCode::OK, 'user_uuid' => $auth['user_uuid']];
    }

    protected function authenticateAdmin(): array
    {
        $auth = AuthMiddleware::verifyAccessToken(isAdmin: true);
        if (!$auth['status']) {
            return $auth;
        }

        $validator = v::validateUserUuid($auth['user_uuid']);
        if (!$validator['status']) {
            return $validator;
        }
        return ['status' => true, 'message' => "Admin is loggedIn", 'code' => StatusCode::OK, 'user_uuid' => $auth['user_uuid']];
    }

    protected function getUser(string $uuid): ?UserModelEntity
    {
        $user = (new UserModel())->get($uuid);
        return $user;
    }


    /**
     * Attempts to decode the JWT from the Authorization header.
     * Handles exceptions for invalid/expired tokens.
     * * @return array|object Decoded token object on success, or an error array on failure.
     */
    public function jwtTokenData(string $action): array|stdClass|null
    {
        if (!isset($_SERVER['HTTP_AUTHORIZATION'])) {
            return [
                'status' => false,
                'code' => StatusCode::UNAUTHORIZED,
                'message' => "This action required authentication",
            ];
        }
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
            // Check for valid token and handle token expiration/invalidation
            $decoded = JWT::decode(
                $token,
                new Key($_ENV['JWT_SECRET_KEY'], $_ENV['JWT_ALGO_ENC'])
            );

            // Check if it's explicitly a refresh token (best practice for refresh endpoint)
            if (!isset($decoded->action) || $decoded->action !== $action) {
                return [
                    'status' => false,
                    'code' => StatusCode::FORBIDDEN,
                    'message' => 'Token is not a valid refresh token.',
                ];
            }

            return $decoded;

        } catch (ExpiredException $e) {
            // For refresh, ExpiredException is expected if the token is valid but expired.
            // We return a failure array to signal the refreshAccessToken method to proceed.
            // NOTE: The refreshAccessToken method must specifically check for this error.
            return [
                'status' => false,
                'code' => StatusCode::UNAUTHORIZED,
                'message' => 'Token has expired.',
                'error' => 'expired_token',
            ];
        } catch (SignatureInvalidException | BeforeValidException | \DomainException $e) {
            // Other validation errors mean the token is completely invalid/forged.
            return [
                'status' => false,
                'code' => StatusCode::UNAUTHORIZED,
                'message' => 'Invalid token signature or format.',
            ];
        } catch (Exception $e) {
            // General failure
            return [
                'status' => false,
                'code' => StatusCode::INTERNAL_SERVER_ERROR,
                'message' => 'Token decoding failed.',
            ];
        }
    }


    protected function generateTokens(string $userUuid, string $action, int $expiresIn, int $refreshExpiresIn, bool $isRefresh = false): array
    {
        $currentTime = time();

        // Access token - short lifespan
        $accessPayload = [
            'iat' => $currentTime,
            'exp' => $currentTime + $expiresIn,
            'iss' => $_ENV['SITE_DOMAIN'],
            'aud' => $_ENV['SITE_DOMAIN'],
            'jti' => bin2hex(random_bytes(16)),
            'action' => $action,
            'ip' => $_SERVER['REMOTE_ADDR'],
            'data' => [
                'user_uuid' => $userUuid
            ],
        ];

        $refresh_expire = $currentTime + $refreshExpiresIn;
        // Refresh token - longer lifespan
        $refreshPayload = [
            'iat' => $currentTime,
            'exp' => $refresh_expire,
            'iss' => $_ENV['SITE_DOMAIN'],
            'aud' => $_ENV['SITE_DOMAIN'],
            'jti' => bin2hex(random_bytes(16)),
            'action' => "user_refresh",
            'data' => [
                'user_uuid' => $userUuid
            ],
        ];

        $accessToken = JWT::encode($accessPayload, $_ENV['JWT_SECRET_KEY'], $_ENV['JWT_ALGO_ENC']);
        if ($isRefresh) {
            $refreshToken = "";
        } else {
            $refreshToken = JWT::encode($refreshPayload, $_ENV['JWT_SECRET_KEY'], $_ENV['JWT_ALGO_ENC']);

            // 👉 Best practice: store refresh token’s jti in DB to allow revocation
            RefreshTokenModel::store($userUuid, $refreshPayload['jti'], $refreshPayload['exp']);
        }

        return [
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
            'expires_in' => $expiresIn,
        ];
    }
}