<?php
namespace MyApp\Controllers;

use MyApp\Models\AdminModel;
use MyApp\Utils\FilePathManagerClass;
use MyApp\Utils\ValidatorHelper as v;
use PH7\JustHttp\StatusCode;
use MyApp\Models\RefreshTokenModel;
use MyApp\Enums\AdminActions;
use MyApp\Utils\AuthMiddleware;
use MyApp\Models\UserModel;
use MyApp\Models\CelebrityModel;
use MyApp\Models\BookingModel;
use MyApp\Validation\Exception\InvalidUserException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AdminController extends FunctionsController
{
    protected AdminModel $model;
    private const DATE_TIME_FORMAT = 'Y-m-d H:i:s';
    // JWT EXPIRED IN 12 HOURS
    private const JWT_TOKEN_EXPIRES_IN = (12 * 60 * 60);
    // JWT EXPIRED IN 30 DAYS
    private const JWT_REFRESH_TOKEN_EXPIRES_IN = (30 * 24 * 60 * 60);

    public function __construct()
    {
        $this->model = new AdminModel();
    }

    public function createAdmin(array $data): array
    {
        $requiredInputs = [
            'firstname',
            'lastname',
            'email',
            'password',
        ];
        $this->validateParams($data, $requiredInputs);
        $validators = [
            v::validateString($data['firstname'], 'Firstname', 2, 50),
            v::validateString($data['lastname'], 'Lastname', 2, 50),
            v::validateString($data['email'], 'Email', 2, 50),
        ];
        $this->validateInputs($validators);
        return ['status' => true, 'message' => 'User created', 'code' => StatusCode::OK];//$this->model->create($data);
    }

    public function create(array $data, string|int|null $id): array
    {
        $auth = $this->authenticateAdmin();
        if (!$auth['status']) {
            return $auth;
        }
        $validator = v::validateUserUuid($auth['user_uuid']);
        if (!$validator['status']) {
            return $validator;
        }

        if ($id) {
            $className = ucfirst(strtolower($id)) . "Controller";
            $namespace = "MyApp\\Controllers";
            $fullClassName = $namespace . "\\" . $className;

            if (class_exists($fullClassName)) {
                $controller = new $fullClassName();
                $controllerData = $controller->createAdmin($data);
                if (!$controllerData) {
                    return ['status' => false, 'message' => "Failed to complete action", 'code' => StatusCode::EXPECTATION_FAILED];
                }
                return $controllerData;
            } else {
                return ['status' => false, 'message' => "Failed to complete action", 'code' => StatusCode::EXPECTATION_FAILED];
            }
        }
        return ['status' => false, 'message' => "Failed to complete action", 'code' => StatusCode::EXPECTATION_FAILED];
    }

    public function get(int|string $uuid): array
    {
        $auth = $this->authenticateAdmin();
        if (!$auth['status']) {
            return $auth;
        }
        $validator = v::validateUserUuid($auth['user_uuid']);
        if (!$validator['status']) {
            return $validator;
        }
        $validator = v::validateUuid($uuid);
        if (!$validator['status']) {
            return $validator;
        }
        $admin = $this->model->get($uuid);
        if (!$admin) {
            return ['status' => false, 'message' => 'User not found', 'code' => StatusCode::NOT_FOUND];
        }
        return [
            'status' => true,
            'message' => 'User found',
            'data' => $admin->__toArray(),
            'code' => StatusCode::OK
        ];
    }

    public function getProfile(): array
    {
        $auth = $this->authenticateAdmin();
        if (!$auth['status']) {
            return $auth;
        }
        $validator = v::validateUserUuid($auth['user_uuid']);
        if (!$validator['status']) {
            return $validator;
        }
        $admin = $this->model->get($auth['user_uuid']);
        if (!$admin) {
            return ['status' => false, 'message' => 'User not found', 'code' => StatusCode::NOT_FOUND];
        }
        return [
            'status' => true,
            'message' => 'User found',
            'data' => $admin->__toArray(),
            'code' => StatusCode::OK
        ];
    }

    public function getAll(string|int|null $id): array
    {
        $auth = $this->authenticateAdmin();
        if (!$auth['status']) {
            return $auth;
        }
        $validator = v::validateUserUuid($auth['user_uuid']);
        if (!$validator['status']) {
            return $validator;
        }
        
        if ($id) {
            $className = ucfirst(strtolower($id)) . "Controller";
            $namespace = "MyApp\\Controllers";
            $fullClassName = $namespace . "\\" . $className;

            if (class_exists($fullClassName)) {
                $controller = new $fullClassName();
                $controllerData = $controller->getAllAdmin();
                if (!$controllerData) {
                    return ['status' => false, 'message' => "Failed to fetch items", 'code' => StatusCode::EXPECTATION_FAILED];
                }
                return $controllerData;
            }else{
                return ['status' => false, 'message' => "Failed to fetch items", 'code' => StatusCode::EXPECTATION_FAILED];
            }
        }
        return ['status' => false, 'message' => "Failed to fetch items", 'code' => StatusCode::EXPECTATION_FAILED];
    }

    public function update(array $data,string|int|null $id): array
    {
        $auth = $this->authenticateAdmin();
        if (!$auth['status']) {
            return $auth;
        }
        $validator = v::validateUserUuid($auth['user_uuid']);
        if (!$validator['status']) {
            return $validator;
        }

        if ($id) {
            $className = ucfirst(strtolower($id)) . "Controller";
            $namespace = "MyApp\\Controllers";
            $fullClassName = $namespace . "\\" . $className;

            if (class_exists($fullClassName)) {
                $controller = new $fullClassName();
                $controllerData = $controller->editAdmin($data);
                if (!$controllerData) {
                    return ['status' => false, 'message' => "Failed to fetch items", 'code' => StatusCode::EXPECTATION_FAILED];
                }
                return $controllerData;
            } else {
                return ['status' => false, 'message' => "Failed to fetch items", 'code' => StatusCode::EXPECTATION_FAILED];
            }
        }
        return ['status' => false, 'message' => "Failed to fetch items", 'code' => StatusCode::EXPECTATION_FAILED];
    }

    public function delete(array $data, string|int|null $id): array
    {
        $auth = $this->authenticateAdmin();
        if (!$auth['status']) {
            return $auth;
        }
        $validator = v::validateUserUuid($auth['user_uuid']);
        if (!$validator['status']) {
            return $validator;
        }

        if ($id) {
            $className = ucfirst(strtolower($id)) . "Controller";
            $namespace = "MyApp\\Controllers";
            $fullClassName = $namespace . "\\" . $className;

            if (class_exists($fullClassName)) {
                $controller = new $fullClassName();
                $controllerData = $controller->deleteAdmin($data);
                if (!$controllerData) {
                    return ['status' => false, 'message' => "Failed to fetch items", 'code' => StatusCode::EXPECTATION_FAILED];
                }
                return $controllerData;
            } else {
                return ['status' => false, 'message' => "Failed to fetch items", 'code' => StatusCode::EXPECTATION_FAILED];
            }
        }
        return ['status' => false, 'message' => "Failed to fetch items", 'code' => StatusCode::EXPECTATION_FAILED];
    }

    public function updateProfile(array $data): array
    {
        $auth = $this->authenticateAdmin();
        if (!$auth['status']) {
            return $auth;
        }
        $validator = v::validateUserUuid($auth['user_uuid']);
        if (!$validator['status']) {
            return $validator;
        }

        $requiredInputs = [
            'id',
        ];
        $this->validateParams($data, $requiredInputs);

        $validator = v::validateUuid($data['id']);
        if (!$validator['status']) {
            return $validator;
        }

        if (isset($_FILES['image'])) {
            $ObjFileMg = new FilePathManagerClass();
            $dir = $ObjFileMg->getDataImageFolder();
            $file = uploadFile($dir, 'image');

            if (empty($file['filename'])) {
                // Handle upload error
                return [
                    'code' => StatusCode::UNPROCESSABLE_ENTITY,
                    'message' => $file['message'],
                    'success' => false,
                ];
            }
            $data['image'] = $file['filename'];
        }

        return $this->model->update($data['id'], $data);
    }

    public function login(array $data): array
    {
        $requiredInputs = [
            'email',
            'password',
        ];
        $this->validateParams($data, $requiredInputs);

        $validators = [
            v::validateEmail($data['email']),
            v::validatePassword($data['password']),
        ];

        $this->validateInputs($validators);

        $user = $this->model->getByEmail($data['email']);
        if (!$user) {
            throw new InvalidUserException("User not found", StatusCode::NOT_FOUND);
        }

        if (!password_verify($data['password'], $user->getPassword())) {
            throw new InvalidUserException("Invalid credentials", StatusCode::UNAUTHORIZED);
        }

        $token = $this->generateTokens(
            userUuid: $user->getUserUuid(), 
            action: "admin_" . AdminActions::LOGIN_JWT->value, 
            expiresIn: (int) self::JWT_TOKEN_EXPIRES_IN, 
            refreshExpiresIn: (int) self::JWT_REFRESH_TOKEN_EXPIRES_IN,
            isRefresh: false 
        );
        $user_data = $user->__toArray();
        unset($user_data['createdAt'], $user_data['updatedAt']);
        return ['status' => true, 'message' => "Login successful", 'code' => StatusCode::OK, 'token' => $token, 'user' => $user_data];
    }


    /**
     * Refreshes the Access Token using a valid Refresh Token.
     */
    public function refreshAccessToken(): array
    {
        $decoded = $this->jwtTokenData("admin_" . AdminActions::LOGIN_JWT->value);

        if (is_array($decoded)) {
            if (isset($decoded['status']) && $decoded['status'] === false) {
                // Returns unauthorized, invalid signature, or other decoding errors.
                return $decoded;
            }
        }

        // Use the token data from the successful decoding ($decoded is an object)
        if (is_object($decoded)) {
            $userUuid = $decoded->data->user_uuid;
            $jti = $decoded->jti ?? null;
        }
        // Or, use the token data from the *expired* token (since jwtTokenData returns an array on failure)
        else {
            return [
                'status' => false,
                'code' => StatusCode::UNAUTHORIZED,
                'message' => 'Refresh token has expired, please log in again.',
            ];
        }

        // --- Revocation Check (Best Practice) ---
        // 🛑 CRITICAL: Before issuing a new token, check if the refresh token's JTI is in the database.
        // If it is, delete it and issue new tokens, preventing reuse.
        // If it's not, the token has already been used or revoked, so fail.
        // var_dump($jti);
        if (!$jti || !RefreshTokenModel::exists($userUuid, $jti)) {
            return [
                'status' => false,
                'code' => StatusCode::UNAUTHORIZED,
                'message' => 'Invalid or revoked refresh token.',
            ];
        }

        // Best Practice: Revoke the old refresh token by deleting the JTI from DB before issuing a new one
        RefreshTokenModel::revoke($jti);


        // ✅ valid refresh, reissue new tokens
        $newTokens = $this->generateTokens(
            userUuid: $userUuid, 
            action: "admin_" . AdminActions::LOGIN_JWT->value, 
            expiresIn: (int) self::JWT_TOKEN_EXPIRES_IN, 
            refreshExpiresIn: (int) self::JWT_REFRESH_TOKEN_EXPIRES_IN,
            isRefresh: true
        );

        return [
            'status' => true,
            'code' => StatusCode::OK,
            'message' => 'Access token refreshed',
            'data' => $newTokens
        ];
    }
    
    public function isLoggedIn(): array
    {
        $auth = $this->authenticateAdmin();
        if (!$auth['status']) {
            return $auth;
        }
        $validator = v::validateUserUuid($auth['user_uuid']);
        if (!$validator['status']) {
            return $validator;
        }
        return ['status' => true, 'message' => '', 'code' => StatusCode::OK];
    }

    public function countData(): array
    {
        $auth = $this->authenticateAdmin();
        if (!$auth['status']) {
            return $auth;
        }
        $validator = v::validateUserUuid($auth['user_uuid']);
        if (!$validator['status']) {
            return $validator;
        }

        $userModel = new UserModel();
        $allUsers = count($userModel->getAll());
        $celebrityModel = new CelebrityModel();
        $allCelebrities = count($celebrityModel->getAll());
        $bookingModel = new BookingModel();
        $allBookings = count($bookingModel->getAll());
        return [
            'status' => true,
            'message' => 'Data found',
            'data' =>
                [
                    'countUsers' => $allUsers,
                    'countCelebrities' => $allCelebrities,
                    'countBookings' => $allBookings
                ],
            'code' => StatusCode::OK
        ];
    }

    public function sendMessage(array $data): array
    {
        $auth = $this->authenticateAdmin();
        if (!$auth['status']) {
            return $auth;
        }
        $validator = v::validateUserUuid($auth['user_uuid']);
        if (!$validator['status']) {
            return $validator;
        }
        $data['sender_uuid'] = $auth['user_uuid'];
        $data['is_user'] = false;
        $ObjChat = new ChatController();
        return $ObjChat->create($data);
    }

    public function getChats()
    {
        $auth = $this->authenticateAdmin();
        if (!$auth['status']) {
            return $auth;
        }
        $validator = v::validateUserUuid($auth['user_uuid']);
        if (!$validator['status']) {
            return $validator;
        }
        $ObjChat = new ChatController();
        return $ObjChat->getMyChats($auth['user_uuid']);
    }

    public function getAllChats()
    {
        $auth = $this->authenticateAdmin();
        if (!$auth['status']) {
            return $auth;
        }
        $validator = v::validateUserUuid($auth['user_uuid']);
        if (!$validator['status']) {
            return $validator;
        }
        $ObjChat = new ChatController();
        return $ObjChat->getAll();
    }
}