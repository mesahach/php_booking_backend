<?php
declare(strict_types=1);

namespace MyApp\Controllers;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use MyApp\Models\UserModel;
use MyApp\Controllers\FunctionsController;
use MyApp\Dal\RefreshTokenDal;
use MyApp\Entity\UserModelEntity;
use MyApp\Utils\ValidatorHelper as v;
use MyApp\Enums\UserAction;
use PH7\JustHttp\StatusCode;
use MyApp\Models\RefreshTokenModel;
use MyApp\Utils\AuthMiddleware;
use MyApp\Validation\Exception\InvalidUserException;
use MyApp\Utils\EmailMessagesClass;
use MyApp\Utils\NotificationManagerClass;
use MyApp\Entity\OtpAuthenticationModelEntity;
use MyApp\DAL\OTPDal;
use stdClass;
use Firebase\JWT\ExpiredException; // <-- Need to catch this
use Firebase\JWT\SignatureInvalidException; // <-- Need to catch this
use Firebase\JWT\BeforeValidException; // <-- Need to catch this
use Respect\Validation\Rules\Lowercase;

class UserController extends FunctionsController
{
    protected UserModel $model;
    // JWT EXPIRED IN 12 HOURS
    private const JWT_TOKEN_EXPIRES_IN = 12 * 60 * 60;
    // JWT EXPIRED IN 30 DAYS
    private const JWT_REFRESH_TOKEN_EXPIRES_IN = 30 * 24 * 60 * 60;

    public function __construct()
    {
        $this->model = new UserModel();
    }

    // ====== Standard CRUD ======
    public function get()
    {
        $auth = AuthMiddleware::verifyAccessToken();
        if (!$auth['status']) {
            return $auth;
        }
        $validator = v::validateUserUuid($auth['user_uuid']);
        if (!$validator['status']) {
            return $validator;
        }
        $user = $this->model->get($auth['user_uuid']);
        if (!$user) {
            return ['status' => false, 'message' => 'User not found', 'code' => StatusCode::NOT_FOUND];
        }
        return [
            'status' => true,
            'message' => 'User found',
            'data' => $user->__toArray(),
            'code' => StatusCode::OK
        ];
    }

    public function getAllAdmin(): array
    {
        $users = $this->model->getAll();
        return [
            'status' => true,
            'message' => 'Users found',
            'data' => $users,
            'code' => StatusCode::OK
        ];
    }

    public function deleteAdmin(array $data): array
    {
        $requiredInputs = [
            'uuid',
        ];
        $this->validateParams($data, $requiredInputs);

        $validator = v::validateUserUuid($data['uuid']);
        if (!$validator['status']) {
            return $validator;
        }

        return $this->model->delete($data['id']);
    }

    public function create(array $data): array
    {
        $requiredInputs = [
            'firstname',
            'lastname',
            'phone',
            'email',
            'password',
        ];
        $this->validateParams($data, $requiredInputs);

        $validators = [
            v::validateString($data['firstname'], 'First Name', 2, 50),
            v::validateString($data['lastname'], 'Last Name', 2, 50),
            v::validatePhone($data['phone']),
            v::validateEmail($data['email']),
            v::validatePassword($data['password']),
        ];

        $this->validateInputs($validators);

        // if (!isset($_SERVER['HTTP_X_API_KEY'])) {
        //     if (!$this->verifyCaptcha($data)) {
        //         return ['status' => false, 'message' => 'Captcha verification failed', 'code' => StatusCode::EXPECTATION_FAILED];
        //     }
        // }

        $validator = $this->isUserExist($data);
        if ($validator) {
            return ['status' => false, 'message' => 'User already exists', 'code' => StatusCode::EXPECTATION_FAILED];
        }
        $data['firstname'] = ucfirst($data['firstname']);
        $data['lastname'] = ucfirst($data['lastname']);
        $data['email'] = strtolower($data['email']);

        $result = $this->model->create($data);
        if ($result['status']) {
            $ObjEmailMessages = new EmailMessagesClass();
            $user_data = $this->model->getByEmail($result['user']['emailAddress']);
            OTPDal::delete($user_data->getUserUuid());
            $ObjAuth = new OtpAuthenticationModelEntity();
            $ObjAuth->setUserUuid($user_data->getUserUuid());
            $ObjAuth->generateOtp();
            $ObjAuth->setExpiresAt(date('Y-m-d H:i:s', time() + 15 * 60));
            $ObjAuth->setCreatedAt(date('Y-m-d H:i:s'));
            OTPDal::create($ObjAuth);

            $token = $ObjAuth->getOtpCode();

            $title = "Welcome to " . siteName;
            $messageInfo = "
                Hi {$user_data->getFirstName()},
                Congratulations on starting your journey with " . siteName . ".
                <p>
                You received this message because {$user_data->getEmail()} signed up for an account on " . siteName . ".
                <br>
                <p>Use the code below to verify your email or click the button below.<br></p>
                <center>
                <b>$token</b>
                </center>
                </p>
                ";
            $messageTemp = $ObjEmailMessages->setWelcomeMessage(
                title: $title,
                messageInfo: $messageInfo,
                token: $token,
                user_data: $user_data
            );

            $ObjNotificationManager = new NotificationManagerClass();
            $ObjNotificationManager->sendEmail(
                title: $title,
                message: $messageTemp,
                userData: $user_data,
                noreply: false,
            );
        }
        return $result;
    }

    public function verifyEmailOTP(array $data): array
    {
        $requiredKeys = [
            'email',
            'auth_code'
        ];
        $this->validateParams($data, $requiredKeys);
        $validator = v::validateEmail($data['email']);
        if (!$validator['status']) {
            return $validator;
        }
        $user_data = $this->model->getByEmail($data['email']);
        $oldAuth = OTPDal::get($user_data->getUserUuid());

        if (!$oldAuth) {
            return ['status' => false, 'message' => 'Invalid OTP', 'code' => StatusCode::EXPECTATION_FAILED];
        }
        if ($oldAuth->getOtpCode() != $data['auth_code']) {
            return ['status' => false, 'message' => 'Invalid OTP', 'code' => StatusCode::EXPECTATION_FAILED];
        }
        if ($oldAuth->getExpiresAt() < date('Y-m-d H:i:s')) {
            OTPDal::delete($user_data->getUserUuid());
            return ['status' => false, 'message' => 'Token Expired', 'code' => StatusCode::UNAUTHORIZED];
        }
        $this->model->update($user_data->getUserUuid(), ['email_auth' => true]);
        OTPDal::delete($user_data->getUserUuid());

        $title = "Congratulations your email has been verified";
        $messageInfo = "
Hi {$user_data->getFirstName()},
Congratulations on starting your journey with " . siteName . ".
<p>
Your Email {$user_data->getEmail()} has been verified.
</p>
";
        $ObjEmailMessages = new EmailMessagesClass();
        $messageTemp = $ObjEmailMessages->setNotificationMessage(
            title: $title,
            messageInfo: $messageInfo,
        );

        $ObjNotificationManager = new NotificationManagerClass();
        $result = $ObjNotificationManager->sendEmail(
            title: $title,
            message: $messageTemp,
            userData: $user_data,
            noreply: false,
        );
        return ['status' => true, 'message' => 'Email verified', 'code' => StatusCode::OK];
    }

    public function resendEmailOTP(array $data)
    {
        $requiredKeys = ['email'];
        $this->validateParams($data, $requiredKeys);

        $user_data = $this->model->getByEmail($data['email']);
        if (!$user_data) {
            return ['status' => false, 'message' => 'User not found', 'code' => StatusCode::NOT_FOUND];
        }
        OTPDal::delete($user_data->getUserUuid());
        $ObjAuth = new OtpAuthenticationModelEntity();

        $ObjAuth->setUserUuid($user_data->getUserUuid());
        $ObjAuth->generateOtp();
        $ObjAuth->setExpiresAt(date('Y-m-d H:i:s', time() + 15 * 60));
        $ObjAuth->setCreatedAt(date('Y-m-d H:i:s'));
        OTPDal::create($ObjAuth);

        $token = $ObjAuth->getOtpCode();
        $title = "Your OTP for email verification";

        $ObjEmailMessages = new EmailMessagesClass();
        $messageTemp = $ObjEmailMessages->setOtpEmail(
            title: $title,
            otpCode: $token,
            expiryMinutes: 15
        );

        $ObjNotificationManager = new NotificationManagerClass();
        $result = $ObjNotificationManager->sendEmail(
            title: $title,
            message: $messageTemp,
            userData: $user_data,
            noreply: false,
        );

        return [
            'status' => $result ? true : false,
            'message' => $result ? 'OTP sent' : "Failed to send OTP",
            'code' => $result ? StatusCode::OK : StatusCode::EXPECTATION_FAILED
        ];
    }

    public function update(array $data)
    {
        $auth = AuthMiddleware::verifyAccessToken();
        if (!$auth['status']) {
            return $auth;
        }
        $validator = v::validateUserUuid($auth['user_uuid']);
        if (!$validator['status']) {
            return $validator;
        }
       
        if (isset($_FILES['userImage']) && !empty($_FILES['userImage']["name"])) {
            $ObjFileMg = new \MyApp\Utils\FilePathManagerClass();
            $dir = $ObjFileMg->getProfileImageFolder();

            $file = uploadFile($dir, 'userImage');
            if ($file['status']) {
                $data['image'] = $file['filename'];
            } else {
                return [
                    'code' => 412,
                    'message' => $file['message'],
                    'status' => false,
                ];
            }
        }

        return $this->model->update($auth['user_uuid'], $data);
    }

    public function updatePassword(array $data)
    {
        $requiredKeys = [
            'current_password',
            'password',
            'confirm_password'
        ];
        $this->validateParams($data, $requiredKeys);
        $auth = AuthMiddleware::verifyAccessToken();
        if (!$auth['status']) {
            return $auth;
        }
        $validator = v::validateUserUuid($auth['user_uuid']);
        if (!$validator['status']) {
            return $validator;
        }
        $validator = [
            v::validatePassword($data['current_password']),
            v::validatePassword($data['password']),
            v::validatePassword($data['confirm_password'])
        ];
        $this->validateInputs($validator);

        if ($data['password'] != $data['confirm_password']) {
            return ['status' => false, 'message' => 'Passwords do not match', 'code' => StatusCode::EXPECTATION_FAILED];
        }

        // check if current password is correct
        $user = $this->model->get($auth['user_uuid']);
        if (!$user) {
            return ['status' => false, 'message' => 'User not found', 'code' => StatusCode::NOT_FOUND];
        }
        if (!password_verify($data['current_password'], $user->getPassword())) {
            return ['status' => false, 'message' => 'Current password is incorrect', 'code' => StatusCode::EXPECTATION_FAILED];
        }

        return $this->model->updatePassword($auth['user_uuid'], $data);
    }

    public function delete(array $data)
    {
        $auth = AuthMiddleware::verifyAccessToken();
        if (!$auth['status']) {
            return $auth;
        }
        $validator = v::validateUserUuid($auth['user_uuid']);
        if (!$validator['status']) {
            return $validator;
        }

        return $this->model->delete($auth['user_uuid']);
    }

    public function sendMessage(array $data)
    {
        $auth = AuthMiddleware::verifyAccessToken();
        if (!$auth['status']) {
            return $auth;
        }
        $validator = v::validateUserUuid($auth['user_uuid']);
        if (!$validator['status']) {
            return $validator;
        }
        $data['sender_uuid'] = $auth['user_uuid'];
        $data['is_user'] = true;
        $ObjChat = new ChatController();
        return $ObjChat->create($data);
    }

    public function getMyChats()
    {
        $auth = AuthMiddleware::verifyAccessToken();
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

    // ====== Custom GET ======
    public function getProfile()
    {
        $auth = AuthMiddleware::verifyAccessToken();
        if (!$auth['status']) {
            return $auth;
        }
        $validator = v::validateUserUuid($auth['user_uuid']);
        if (!$validator['status']) {
            return $validator;
        }
        return $this->get();
    }

    public function getByEmail(?string $email)
    {
        if (!$email) {
            return ['status' => false, 'message' => 'Email required', 'code' => StatusCode::EXPECTATION_FAILED];
        }

        $user = $this->model->getByEmail($email);
        if (!$user) {
            return ['status' => false, 'message' => 'User not found', 'code' => StatusCode::EXPECTATION_FAILED];
        }

        $user = $user->__toArray();

        return ['status' => true, 'message' => 'User found', 'data' => $user, 'code' => StatusCode::OK];
    }

    // ====== Custom POST ======
    public function login(array $data)
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

        $token = "";
        if (!$user->getEmailAuth()) {
            $this->resendEmailOTP(['email' => $user->getEmail()]);
        }else{
            $token = $this->generateTokens(
                userUuid: $user->getUserUuid(),
                action: "user_" . UserAction::LOGIN->value,
                expiresIn: self::JWT_TOKEN_EXPIRES_IN,
                refreshExpiresIn: self::JWT_REFRESH_TOKEN_EXPIRES_IN,
                isRefresh: false
            );
        }
        $user_data = $user->__toArray();
        unset($user_data['createdAt'], $user_data['updatedAt']);
        $message = $user->getEmailAuth() ? "Login successful" : "Email is not verified";
        return ['status' => true, 'message' => $message, 'code' => $user->getEmailAuth() ? StatusCode::OK : StatusCode::UNAUTHORIZED, 'token' => $token, 'user' => $user_data];
    }

    /**
     * Refreshes the Access Token using a valid Refresh Token.
     */
    public function refreshAccessToken(): array
    {
        $decoded = $this->jwtTokenData("user_" . UserAction::LOGIN->value);

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
            action: "user_" . UserAction::LOGIN->value,
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

    public function logout()
    {
        $decoded = $this->jwtTokenData("user_" . UserAction::LOGIN->value);//(UserAction::LOGOUT->value);

        if (is_array($decoded)) {
            if (isset($decoded['status']) && $decoded['status'] === false) {
                // Returns unauthorized, invalid signature, or other decoding errors.
                return $decoded;
            }
        }

        $task = RefreshTokenDal::revoke($decoded->jti);
        return [
            'status' => $task,
            'message' => $task ? 'Logout successful' : 'Failed to logout',
            'code' => $task ? StatusCode::OK : StatusCode::EXPECTATION_FAILED,
        ];
    }

    public function isUserExist(array $data): bool
    {
        $requiredKeys = ['email'];
        $this->validateParams($data, $requiredKeys);
        if (empty($data['email'])) {
            return false;
        }

        return $this->model->isUserExist($data['email']);
    }

    public function isLoggedIn(): array
    {
        $auth = AuthMiddleware::verifyAccessToken();
        if (!$auth['status']) {
            return $auth;
        }
        $validator = v::validateUserUuid($auth['user_uuid']);
        if (!$validator['status']) {
            return $validator;
        }
        return ['status' => true, 'message' => '', 'code' => StatusCode::OK];
    }

    public function isAdmin(string $domain)
    {
        $this->model->checkIfIsFirstTime($domain);
    }

    // ====== Custom PUT ======
    public function changePassword(array $data)
    {
        $auth = AuthMiddleware::verifyAccessToken();
        if (!$auth['status']) {
            return $auth;
        }
        $validator = v::validateUserUuid($auth['user_uuid']);
        if (!$validator['status']) {
            return $validator;
        }
        $requiredInputs = [
            'password',
            'new_password',
            'confirm_password'
        ];
        $this->validateParams($data, $requiredInputs);

        $validators = [
            v::validatePassword($data['password']),
            v::validatePassword($data['new_password']),
            v::validatePassword($data['confirm_password']),
        ];

        $this->validateInputs($validators);

        $user = $this->model->get($auth['user_uuid']);

        if (!password_verify($data['password'], $user->getPassword())) {
            return ['status' => false, 'message' => 'Invalid password', 'code' => StatusCode::UNAUTHORIZED];
        }

        return $this->model->changePassword($data['id'], $data['new_password']);
    }

    // ====== Custom DELETE ======
    public function deactivate(array $data)
    {
        $requiredInputs = [
            'deactivate',
        ];
        $this->validateParams($data, $requiredInputs);

        $validators = [
            v::validateUserUuid($data['deactivate']),
        ];

        $this->validateInputs($validators);

        $auth = AuthMiddleware::verifyAccessToken();
        $validator = v::validateUserUuid($auth['user_uuid']);

        if (!$auth['status']) {
            return $auth;
        }

        if (!$validator['status']) {
            return $validator;
        }

        return $this->model->deactivate($auth['user_uuid']);
    }
}