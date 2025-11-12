<?php 
namespace MyApp\Utils;

use MyApp\Utils\EmailMessagesClass;
use MyApp\Classes\Models\messagesClass;
use MyApp\DAL\OTPDal;
use MyApp\DAL\UserDal;
use MyApp\Entity\UserModelEntity;
use MyApp\Entity\OtpAuthenticationModelEntity;

use PHPMailer\PHPMailer\PHPMailer;
use Exception;
use MyApp\Models\UserModel;
use PH7\JustHttp\StatusCode;

class NotificationManagerClass 
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
                    'code' => 422,
                    'message' => "Missing required parameter: $key",
                ], StatusCode::UNPROCESSABLE_ENTITY);
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

    
    public function registerDeviceToken(int $user_id, array $data)
    {
        $requiredKeys = ['deviceToken'];
        $this->validateParams($data, $requiredKeys);

        $deviceTokenClass = new \MyApp\Utils\DeviceTokenClass();

        $saved = $deviceTokenClass->saveDeviceToken($user_id, $data['deviceToken']);

        if ($saved) {
            return ['code' => 200, 'message' => 'Device token saved successfully.'];
        } else {
            return ['code' => 500, 'message' => 'Failed to save device token.'];
        }
    }

    /**
     * Send a push notification to a given device token
     *
     * @param array $data
     * - deviceToken: the FCM token of the device to send the notification to
     * - title: the title of the notification
     * - body: the body of the notification
     * - extraData (optional): a key-value pair of additional data to send with the notification e.g { "extraData": { "route": "/chat/123" } }
     *
     * @return array
     */
    public function sendPushNotification(int $user_id, array $data): array
    {
        $requiredKeys = ['title', 'body'];
        $this->validateParams($data, $requiredKeys);

        $projectId = $_ENV['FIREBASE_PROJECT_ID'];
        $serviceAccountPath = __DIR__ . '/Firebase/notification_key.json';

        $fcm = new \MyApp\Utils\Firebase\FCMService($projectId, $serviceAccountPath);

        $deviceTokenClass = new \MyApp\Utils\DeviceTokenClass();
        $tokens = $deviceTokenClass->getUserTokens($user_id); // ✅ Get all tokens for this user

        $title = $data['title'];
        $body = $data['body'];
        $extraData = $data['extraData'] ?? [];

        $success = true;
        foreach ($tokens as $token) {
            $sent = $fcm->sendPushNotification($token, $title, $body, $extraData);
            if (!$sent) {
                $success = false;
            }
        }

        if ($success) {
            return ['code' => 200, 'message' => 'Notification sent successfully.'];
        } else {
            return ['code' => 500, 'message' => 'Failed to send notification to some devices.'];
        }
    }

    public function resendEmailCode(string $uuid)
    {
        $ObjAuth = new OtpAuthenticationModelEntity();
        $ObjAuth->setUserUuid($uuid);
        $ObjAuth->generateOtp();
        $ObjAuth->setExpiresAt(date('Y-m-d H:i:s', time() + 15 * 60));
        $ObjAuth->setCreatedAt(date('Y-m-d H:i:s'));
        OTPDal::create($ObjAuth);

        $token = $ObjAuth->getOtpCode();

        $title = "Email verification";
        $ObjMessages = new EmailMessagesClass();
        $message = $ObjMessages->setOtpEmail(title: $title, otpCode: $token, expiryMinutes: 15);

        $user_data = UserDal::get($ObjAuth->getUserUuid());

        if ($this->sendEmail($title, $message, $user_data)) {
            return ['code' => 200, 'message' => "New code is sent to your email"];
        } else {
            return ['code' => 401, 'message' => 'Failed to send new code'];
        }
    }

    /**
     * Send an email.
     *
     * @param string $title Email subject.
     * @param string $message Email body.
     * @param UserModelEntity $userData Sender data array.
     * @param bool $noreply Whether to use noreply email.
     * @param string|null $senderEmail Sender email.
     * @return bool True if email sent successfully, false otherwise.
     */
    public function sendEmail(
       string $title,
       string $message,
        UserModelEntity $userData,
        bool $noreply = false,
        ?string $senderEmail = null,
    ): bool {
        $mail = new PHPMailer(true);
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;

        if (siteDomain == "localhost" || siteDomain == "127.0.0.1" || siteDomain == "127.0.0.20") {
            $domain = "mtechsciverse.com";
        } else {
            $domain = siteDomain;
        }

        if ($senderEmail != null) {
            // Check if email contains @localhost
            if (strpos($senderEmail, '@localhost') !== false) {
                // Extract username part before @localhost
                $username = substr($senderEmail, 0, strpos($senderEmail, '@localhost'));
                // Replace with @mtechsciverse.com
                $senderEmail = $username . '@mtechsciverse.com';
            }
        } else {
            $senderEmail = $noreply ? "noreply@$domain" : "info@$domain";
        }
        // $domain = $_ENV['SITE_DOMAIN'];

        $senderName = siteName;
        $senderPass = $_ENV['EMAIL_PASS'];

        $receiverEmail = $userData->getEmail();
        $receiverName = $userData->getFirstName();

        // Server settings
        $mail->isSMTP();
        $mail->CharSet = 'UTF-8';
        $mail->Encoding = 'base64';

        $mail->Host = $domain;
        $mail->SMTPAuth = true;
        $mail->Username = $senderEmail;
        $mail->Password = $senderPass;
        $mail->Port = 465;

        // Recipients
        $mail->setFrom($senderEmail, $senderName);
        $mail->addAddress($receiverEmail, $receiverName);
        $mail->addReplyTo($senderEmail, $senderName);
        // $mail->addCC($senderEmail);
        // $mail->addBCC($senderEmail);

        // Content
        $mail->isHTML(true);
        $mail->Subject = $title;
        $mail->Body = $message;

        // Send email
        try {
            return $mail->send();
        } catch (Exception $e) {
            return false;
        }
    }

    public function sendSms(array $data, string $phoneNum = "")
    {
        // $code = 412;
        // try {
        //     $requiredInputs = [
        //         'message',
        //     ];
        //     $this->validateParams($data, $requiredInputs);

        //     $ObjSms = new SmsSendingClass(secretKey: $_ENV['gatewayapiToken']);
        //     $ObjUser = new UserDataClass(['user_id' => $user_id]);

        //     $user_data = $ObjUser->getUserData();
        //     $phone = empty($phoneNum) ? $user_data['phoneCode'] . $user_data['phone'] : $phoneNum;
        //     $data = $ObjSms->sendSmsGT(phone: $phone, message: $data['message']);

        //     return $data;
        // } catch (\Throwable $e) {
        //     return false;
        // }
    }

}
