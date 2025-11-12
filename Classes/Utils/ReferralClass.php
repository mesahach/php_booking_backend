<?php
namespace MyApp\Utils;

use PDO;
use PDOException;

class ReferralClass
{
    private $dbConn;

    public function __construct()
    {
        $this->dbConn = PDO::connect("mysql:host=localhost;dbname=live_chat_api", "root", "");
    }

    /**
     * Fetch referral stats for a user
     */
    public function getReferralStats(int $userRef): array
    {
        try {
            // 1. Total referrals (count of invitees)
            $sql = "SELECT COUNT(*) 
                    FROM customers 
                    WHERE referrer = :user_id";
            $stmt = $this->dbConn->prepare($sql);
            $stmt->bindValue(':user_id', $userRef, PDO::PARAM_STR);
            $stmt->execute();
            $totalReferrals = (int) $stmt->fetchColumn();

            // 2. Total referral earnings
            $sql = "SELECT COALESCE(SUM(amount), 0) 
                    FROM transactions 
                    WHERE user_id = :user_id 
                      AND type = 'Referral Bonus'";
            $stmt = $this->dbConn->prepare($sql);
            $stmt->bindValue(':user_id', $userRef, PDO::PARAM_STR);
            $stmt->execute();
            $totalEarnings = (float) $stmt->fetchColumn();

            // 3. Total traders (invitees that made ≥1 transaction)
            $sql = "SELECT COUNT(DISTINCT t.user_id)
                    FROM transactions t
                    INNER JOIN customers c ON t.user_id = c.id
                    WHERE c.referrer = :user_id";
            $stmt = $this->dbConn->prepare($sql);
            $stmt->bindValue(':user_id', $userRef, PDO::PARAM_STR);
            $stmt->execute();
            $totalTraders = (int) $stmt->fetchColumn();

            return [
                'totalReferrals' => $totalReferrals,
                'totalEarnings' => "$".number_format($totalEarnings, 2),
                'totalTraders' => $totalTraders
            ];
        } catch (PDOException $e) {
            // fallback: return zeros if query fails
            return [
                'totalReferrals' => "0",
                'totalEarnings' => "$0.0",
                'totalTraders' => "0",
            ];
        }
    }
}