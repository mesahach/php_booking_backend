<?php
namespace MyApp\Utils;

use Exception;
use PDO;

class DeviceTokenClass
{
    private $dbConn;

    public function __construct()
    {
        $this->dbConn = PDO::connect("mysql:host=localhost;dbname=live_chat_api", "root", "");

        // Create table if not exists
        $this->dbConn->exec("CREATE TABLE IF NOT EXISTS device_tokens (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                device_token VARCHAR(255) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                UNIQUE KEY unique_user_device (user_id, device_token)
            )
        ");
    }

    /**
     * Save or update a user's device token
     */
    public function saveDeviceToken(int $userId, string $deviceToken): bool
    {
        try {
            $stmt = $this->dbConn->prepare("INSERT INTO device_tokens (user_id, device_token) 
                VALUES (:user_id, :device_token)
                ON DUPLICATE KEY UPDATE device_token = :device_token_update, updated_at = CURRENT_TIMESTAMP
            ");
            return $stmt->execute([
                ':user_id' => $userId,
                ':device_token' => $deviceToken,
                ':device_token_update' => $deviceToken,
            ]);
        } catch (Exception $e) {
            throw new Exception("Error saving device token: " . $e->getMessage());
        }
    }

    /**
     * Get all tokens for a user (in case they use multiple devices)
     */
    public function getUserTokens(int $userId): array
    {
        $stmt = $this->dbConn->prepare("SELECT device_token FROM device_tokens WHERE user_id = :user_id");
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * Get all tokens in the system (useful for broadcast notifications)
     */
    public function getAllTokens(): array
    {
        $stmt = $this->dbConn->query("SELECT device_token FROM device_tokens");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * Delete a user's token (e.g., on logout)
     */
    public function deleteUserToken(int $userId, string $deviceToken): bool
    {
        $stmt = $this->dbConn->prepare("DELETE FROM device_tokens WHERE user_id = :user_id AND device_token = :device_token");
        return $stmt->execute([
            ':user_id' => $userId,
            ':device_token' => $deviceToken,
        ]);
    }
}