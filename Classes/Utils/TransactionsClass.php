<?php
namespace MyApp\Utils;

use Exception;
use PDO;

class TransactionsClass 
{
    private $user_id;
    private $type;
    private $dbConn;

    public function __construct()
    {
        // parent::__construct($_ENV['DB_HOST'], $_ENV['DB_NAME'], $_ENV['DB_USER'], $_ENV['DB_PASS']);
        $this->dbConn = PDO::connect("mysql:host=localhost;dbname=live_chat_api", "root", "");

        $this->checkAndCreateTable(); // Ensure the table is created

        $arguments = func_get_args();
        if (!empty($arguments)) {
            foreach ($arguments[0] as $key => $property) {
                if (property_exists($this, $key)) {
                    $this->{$key} = $property;
                }
            }
        }
    }

    public function __destruct()
    {
        $this->dbConn = null;
    }

    private function checkAndCreateTable()
    {
        $sql = "CREATE TABLE IF NOT EXISTS transactions (
                id INT(11) AUTO_INCREMENT PRIMARY KEY,
                user_id INT(11) NOT NULL,
                transactionId VARCHAR(110) NOT NULL,
                type VARCHAR(100) DEFAULT NULL,
                status VARCHAR(50),
                amount DECIMAL(20, 2) NOT NULL,
                currencyCode VARCHAR(10) DEFAULT NULL,
                recipientPhone VARCHAR(20) DEFAULT NULL,
                recipientEmail VARCHAR(100) DEFAULT NULL,
                customIdentifier VARCHAR(255) DEFAULT NULL,
                exchangeRate DECIMAL(10, 2) DEFAULT NULL,
                balanceUpdated BOOLEAN NOT NULL DEFAULT FALSE,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                additionalData JSON -- For storing additional/unique data
            )";

        $this->dbConn->exec($sql); // Execute the SQL query
    }

    public function setType($type)
    {
        $this->type = ucfirst($type);
    }

    public function getTransactionsPagination(int $page = 1, $min = null, $max = null): array
    {
        $recordsPerPage = 20;

        // Build dynamic WHERE clause
        $conditions = [];
        $params = [];

        if ($min !== null) {
            $conditions[] = "amount >= :min";
            $params[':min'] = $min;
        }

        if ($max !== null) {
            $conditions[] = "amount <= :max";
            $params[':max'] = $max;
        }

        $where = $conditions ? "WHERE " . implode(" AND ", $conditions) : "";

        // Count total rows
        $countStmt = $this->dbConn->prepare("SELECT COUNT(*) as total FROM transactions $where");
        $countStmt->execute($params);
        $totalRows = (int) $countStmt->fetchColumn();

        $totalPages = max(1, ceil($totalRows / $recordsPerPage));
        $page = max(1, min($page, $totalPages));

        $fromRecordNum = ($page - 1) * $recordsPerPage;

        // Fetch paginated data
        $sql = "SELECT * FROM transactions $where 
            ORDER BY id DESC 
            LIMIT :fromRecordNum, :recordsPerPage";

        $stmt = $this->dbConn->prepare($sql);

        // bind filter params
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        // bind pagination params
        $stmt->bindValue(':fromRecordNum', $fromRecordNum, PDO::PARAM_INT);
        $stmt->bindValue(':recordsPerPage', $recordsPerPage, PDO::PARAM_INT);

        $stmt->execute();
        $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($transactions as &$item) {
            unset($item['user_id']);
        }

        return [
            "data" => $transactions,
            "pagination" => [
                "current_page" => $page,
                "prev_page" => ($page > 1) ? $page - 1 : null,
                "next_page" => ($page < $totalPages) ? $page + 1 : null,
                "total_items" => $totalRows,
                "total_pages" => $totalPages,
            ]
        ];
    }

    /**
     * Get paginated transactions with filters.
     *
     * @param int   $page        Page number (default 1)
     * @param int   $recordsPerPage Number of records per page (default 20)
     * @param float|null $minAmount Minimum amount filter
     * @param float|null $maxAmount Maximum amount filter
     * @param string|null $type Transaction type filter
     * @param string|null $startDate Start date (Y-m-d format)
     * @param string|null $endDate End date (Y-m-d format)
     *
     * @return array
     */
    public function getTransactionsPaginationSearch(
        int $page = 1,
        int $recordsPerPage = 20,
        ?float $minAmount = null,
        ?float $maxAmount = null,
        ?string $type = null,
        ?string $startDate = null,
        ?string $endDate = null,
        ?string $status = null
    ): array {
        $page = max(1, $page);
        $recordsPerPage = max(1, $recordsPerPage);

        // 1) Build WHERE conditions dynamically
        $conditions = [];
        $params = [];

        if ($minAmount !== null) {
            $conditions[] = "amount >= :minAmount";
            $params[':minAmount'] = $minAmount;
        }

        if ($maxAmount !== null) {
            $conditions[] = "amount <= :maxAmount";
            $params[':maxAmount'] = $maxAmount;
        }

        if ($type !== null && $type !== '') {
            $conditions[] = "type = :type";
            $params[':type'] = ucfirst($type);
        }

        if ($status !== null && $status !== '') {
            $conditions[] = "status = :status";
            $params[':status'] = $status;
        }

        if ($startDate !== null) {
            $conditions[] = "DATE(created_at) >= :startDate";
            $params[':startDate'] = $startDate;
        }

        if ($endDate !== null) {
            $conditions[] = "DATE(created_at) <= :endDate";
            $params[':endDate'] = $endDate;
        }

        $where = $conditions ? "WHERE " . implode(" AND ", $conditions) : "";

        // 2) Get total items
        $countSql = "SELECT COUNT(*) FROM transactions $where";
        $countStmt = $this->dbConn->prepare($countSql);
        foreach ($params as $key => $value) {
            $countStmt->bindValue($key, $value);
        }
        $countStmt->execute();
        $totalItems = (int) $countStmt->fetchColumn();

        // 3) Pagination calculations
        $totalPages = ($totalItems > 0) ? (int) ceil($totalItems / $recordsPerPage) : 1;
        if ($page > $totalPages) {
            $page = $totalPages;
        }
        $offset = ($page - 1) * $recordsPerPage;

        // 4) Fetch transactions (safe inline LIMIT values)
        $sql = "SELECT *
            FROM transactions
            $where
            ORDER BY id DESC
            LIMIT {$offset}, {$recordsPerPage}";

        $stmt = $this->dbConn->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
        $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

        // 5) Return result with pagination
        return [
            'data' => array_values($transactions),
            'pagination' => [
                'current_page' => $page,
                'prev_page' => ($page > 1) ? $page - 1 : null,
                'next_page' => ($page < $totalPages) ? $page + 1 : null,
                'total_pages' => $totalPages,
                'total_items' => $totalItems,
                'per_page' => $recordsPerPage,
            ],
            'filters' => [
                'min_amount' => $minAmount,
                'max_amount' => $maxAmount,
                'type' => $type,
                'start_date' => $startDate,
                'end_date' => $endDate,
            ]
        ];
    }

    public function getChartData(string $type): array
    {
        $labels = [];
        $dataset1 = [];
        $dataset2 = [];

        switch ($type) {
            case 'week':
                // Group by day of current week
                $stmt = $this->dbConn->prepare("SELECT DAYNAME(created_at) AS label, SUM(amount) as total
                FROM transactions
                WHERE YEARWEEK(created_at, 1) = YEARWEEK(CURDATE(), 1)
                GROUP BY DAYOFWEEK(created_at)
                ORDER BY created_at
            ");
                $stmt->execute();
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

                foreach ($rows as $row) {
                    $labels[] = $row['label'];
                    $dataset1[] = (float) $row['total'];
                    $dataset2[] = (float) ($row['total'] * 0.7); // example 2nd dataset
                }
                break;

            case 'year':
                // Group by year
                $stmt = $this->dbConn->prepare("SELECT YEAR(created_at) AS label, SUM(amount) as total
                FROM transactions
                GROUP BY YEAR(created_at)
                ORDER BY YEAR(created_at)
            ");
                $stmt->execute();
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

                foreach ($rows as $row) {
                    $labels[] = $row['label'];
                    $dataset1[] = (float) $row['total'];
                    $dataset2[] = (float) ($row['total'] * 0.6); // example 2nd dataset
                }
                break;

            default: // month
                // Group by month of current year
                $stmt = $this->dbConn->prepare("SELECT MONTHNAME(created_at) AS label, SUM(amount) as total
                FROM transactions
                WHERE YEAR(created_at) = YEAR(CURDATE())
                GROUP BY MONTH(created_at)
                ORDER BY MONTH(created_at)
            ");
                $stmt->execute();
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

                foreach ($rows as $row) {
                    $labels[] = $row['label'];
                    $dataset1[] = (float) $row['total'];
                    $dataset2[] = (float) ($row['total'] * 0.8); // example 2nd dataset
                }
                break;
        }

        return [
            "labels" => $labels,
            "datasets" => [
                [
                    "data" => $dataset1,
                    "borderColor" => "#3EBF81",
                    "fill" => false
                ],
                [
                    "data" => $dataset2,
                    "borderColor" => "#0CAF60",
                    "fill" => true
                ]
            ]
        ];
    }

    public function getDonutData(string $type = 'month'): array
    {
        
        // Build WHERE clause based on filter
        $where = '';
        switch ($type) {
            case 'week':
                $where = "WHERE YEARWEEK(created_at, 1) = YEARWEEK(CURDATE(), 1)";
                break;
            case 'month':
                $where = "WHERE YEAR(created_at) = YEAR(CURDATE()) AND MONTH(created_at) = MONTH(CURDATE())";
                break;
            case 'year':
                $where = "WHERE YEAR(created_at) = YEAR(CURDATE())";
                break;
            default:
                $where = ''; // all-time
        }

        // Query revenue grouped by service type
        $stmt = $this->dbConn->prepare("SELECT type, SUM(amount) as total
        FROM transactions
        $where
        GROUP BY type
    ");
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $labels = [];
        $series = [];

        foreach ($rows as $row) {
            $labels[] = ucfirst(str_replace("_", " ", $row['type']));
            $series[] = (float) $row['total'];
        }

        return [
            "labels" => $labels,
            "series" => $series
        ];
    }

    public function getTransactionUserById($user_id, $id)
    {
        $stmt = $this->dbConn->prepare("SELECT * FROM `transactions` WHERE `user_id` = :user_id AND id = :id");
        $stmt->bindValue(":user_id", $user_id, PDO::PARAM_INT);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Save transaction to the database
     */
    public function saveTransaction(array $transactionData)
    {
        $sql = "INSERT INTO transactions (user_id,
                    transactionId, type, status, amount, currencyCode, recipientPhone, 
                    recipientEmail, customIdentifier, exchangeRate, created_at, additionalData
                ) VALUES (:user_id,
                    :transactionId, :type, :status, :amount, :currencyCode, :recipientPhone, 
                    :recipientEmail, :customIdentifier, :exchangeRate, :created_at, :additionalData
                )";

        $stmt = $this->dbConn->prepare($sql);

        // Prepare additional data as JSON
        $additionalData = json_encode($transactionData['additionalData']);

        // Bind parameters
        $stmt->bindParam(':user_id', $this->user_id, PDO::PARAM_INT);
        $stmt->bindParam(':transactionId', $transactionData['transactionId'], PDO::PARAM_INT);
        $stmt->bindValue(':type', ucfirst($this->type), PDO::PARAM_STR);
        $stmt->bindParam(':status', $transactionData['status'], PDO::PARAM_STR);
        $stmt->bindParam(':amount', $transactionData['amount'], PDO::PARAM_STR);
        $stmt->bindParam(':currencyCode', $transactionData['currencyCode'], PDO::PARAM_STR);
        $stmt->bindParam(':recipientPhone', $transactionData['recipientPhone'], PDO::PARAM_STR);
        $stmt->bindParam(':recipientEmail', $transactionData['recipientEmail'], PDO::PARAM_STR);
        $stmt->bindParam(':customIdentifier', $transactionData['customIdentifier'], PDO::PARAM_STR);
        $stmt->bindParam(':created_at', $transactionData['created_at'], PDO::PARAM_STR);
        $stmt->bindParam(':exchangeRate', $transactionData['exchangeRate'], PDO::PARAM_STR);
        $stmt->bindParam(':additionalData', $additionalData, PDO::PARAM_STR);

        // Execute statement
        return $stmt->execute();
    }

    public function getTransactionById($id)
    {
        $stmt = $this->dbConn->prepare("SELECT 
         t.*, 
            u.currency 
        FROM 
            transactions t
        JOIN 
            users u 
        ON 
            t.user_id = u.id
		WHERE t.id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAllTransactionsByStatus($status)
    {
        $stmt = $this->dbConn->prepare("SELECT 
        t.*, 
            u.currency 
        FROM 
            transactions t
        JOIN 
            users u 
        ON 
            t.user_id = u.id 
		WHERE `status` = :id AND `user_id` = :user_id");
        $stmt->bindParam(':user_id', $this->user_id, PDO::PARAM_INT);
        $stmt->bindParam(':id', $status);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllTransactions(): array
    {
        // Join the transactions table with the users table to include the currency
        $stmt = $this->dbConn->prepare("SELECT 
            t.*, 
            u.currency 
        FROM 
            transactions t
        JOIN 
            users u 
        ON 
            t.user_id = u.id
        ORDER BY 
            t.id DESC
    ");

        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $data;
    }


    /**
     * Store transaction based on its type.
     */
    public function storeTransaction(array $data, string $type)
    {
        $this->setType($type);
        switch ($type) {
            case 'Gift Card':
                $transactionData = $this->prepareGiftCardTransaction($data);
                break;
            case 'airtime':
                $transactionData = $this->prepareAirtimeTransaction($data);
                break;
            case 'utility':
                $transactionData = $this->prepareUtilityTransaction($data);
                break;
            case "Crypto Deposit":
                $transactionData = $this->prepareCryptoTransaction($data);
                break;

            case "Sell Crypto":
                $transactionData = $this->prepareBuyAndSellCryptoTransaction($data);
                break;

            case "Buy Crypto":
                $transactionData = $this->prepareBuyAndSellCryptoTransaction($data);
                break;

            case "Bank Deposit":
                $transactionData = $this->prepareKoraBankDeposit($data);
                break;

            case "Card Funding":
                $transactionData = $this->prepareKCardFunding($data);
                break;

            case "Card Withdrawal":
                $transactionData = $this->prepareKCardFunding($data);
                break;

            case "Card Creation":
                $transactionData = $this->prepareKCardCreation($data);
                break;

            default:
                throw new Exception("Unsupported transaction type");
        }

        // Save the transaction
        return $this->saveTransaction($transactionData);
    }

    /**
     * Dynamically prepare transaction data based on the type.
     */
    private function prepareTransactionData(string $type, array $data)
    {
        $method = 'prepare' . ucfirst($type) . 'Transaction';

        if (!method_exists($this, $method)) {
            throw new Exception("Unsupported transaction type: $type");
        }

        return $this->{$method}($data);
    }

    /**
     * Prepares data for a buy or sell crypto transaction.
     *
     * @param array $data Transaction data from the request or API.
     * @return array Prepared transaction data ready for processing.
     * @throws \InvalidArgumentException If required fields are missing or invalid.
     */
    public function prepareBuyAndSellCryptoTransaction(array $data): array
    {
        // Validate required fields
        // if (
        //     !isset($data['transactionId'], $data['status'], $data['amount'], $data['type']) ||
        //     !in_array($data['type'], ['Buy Crypto', 'Sell Crypto'], true)
        // ) {
        //     throw new InvalidArgumentException("Invalid or missing fields. Provide transactionId, status, amount, and a valid type (Buy Crypto or Sell Crypto).");
        // }

        // Prepare the transaction data
        $preparedData = [
            'transactionId' => $data['transactionId'],
            'status' => $data['status'],
            'amount' => (float) $data['amount'],
            'currencyCode' => $data['currencyCode'] ?? null, // Optional
            'recipientEmail' => $data['recipientEmail'] ?? null, // Optional
            'recipientPhone' => $data['recipientPhone'] ?? null, // Optional
            'type' => $data['type'],
            "customIdentifier" => $data['customIdentifier'],
            'exchangeRate' => $data['exchangeRate'],
            'additionalData' => json_encode($data['additionalData'] ?? []), // Encode additional data as JSON
            'created_at' => date('Y-m-d H:i:s'), // Store the current timestamp
        ];

        // Return the prepared data
        return $preparedData;
    }
    /**
     * Prepare gift card transaction data.
     */
    private function prepareGiftCardTransaction(array $data)
    {
        return [
            'transactionId' => $data['transactionId'],
            'status' => $data['status'],
            'amount' => $data['amount'],
            'currencyCode' => $data['currencyCode'],
            'recipientPhone' => $data['recipientPhone'],
            'recipientEmail' => $data['recipientEmail'],
            'customIdentifier' => $data['customIdentifier'],
            'created_at' => $data['transactionCreatedTime'],
            'additionalData' => [
                'product' => $data['product']
            ]
        ];
    }

    /**
     * Prepare airtime transaction data.
     */
    private function prepareAirtimeTransaction(array $data)
    {
        return [
            'transactionId' => $data['transactionId'],
            'status' => $data['status'],
            'amount' => $data['requestedAmount'],
            'currencyCode' => $data['requestedAmountCurrencyCode'],
            'recipientPhone' => $data['recipientPhone'],
            'recipientEmail' => $data['recipientEmail'],
            'customIdentifier' => $data['customIdentifier'],
            'created_at' => $data['transactionDate'],
            'additionalData' => [
                'pinDetail' => $data['pinDetail']
            ]
        ];
    }

    /**
     * Prepare utility (formerly payment processing) transaction data.
     */
    private function prepareUtilityTransaction(array $data)
    {
        return [
            'transactionId' => $data['transaction']['id'],
            'status' => $data['transaction']['status'],
            'amount' => $data['transaction']['amount'],
            'currencyCode' => $data['transaction']['amountCurrencyCode'], // Storing currency code
            'recipientPhone' => null, // No recipient phone for utility transactions
            'recipientEmail' => null,
            'customIdentifier' => null,
            'created_at' => $data['transaction']['submittedAt'],
            'additionalData' => [
                'referenceId' => $data['transaction']['referenceId'],
                'code' => $data['code'],
                'message' => $data['message'],
                'billDetails' => [
                    'billerName' => $data['transaction']['billDetails']['billerName'],
                    'serviceType' => $data['transaction']['billDetails']['serviceType'],
                    'token' => $data['transaction']['billDetails']['pinDetails']['token'],
                    'billerCountryCode' => $data['transaction']['billDetails']['billerCountryCode'] // Store biller currency
                ],
                // 'balanceInfo' => [
                //     'oldBalance' => $data['transaction']['balanceInfo']['oldBalance'],
                //     'newBalance' => $data['transaction']['balanceInfo']['newBalance'],
                //     'currencyCode' => $data['transaction']['balanceInfo']['currencyCode']
                // ]
            ]
        ];
    }

    private function prepareKoraBankDeposit(array $data)
    {
        return [
            'transactionId' => $data['reference'],
            'status' => ucfirst(string: $data['status']),
            'amount' => $data['amount'],
            'currencyCode' => $data['currency'],
            'recipientPhone' => null,
            'recipientEmail' => null,
            'customIdentifier' => $data['virtual_bank_account_details']['virtual_bank_account']['account_number'],
            'created_at' => $this->convertToDateTime(isoDate: $data['transaction_date']),
            'additionalData' => [
                'senderActNumber' => $data['virtual_bank_account_details']['payer_bank_account']['account_number'],
                'senderActName' => $data['virtual_bank_account_details']['payer_bank_account']['account_name'],
                'senderBank' => $data['virtual_bank_account_details']['payer_bank_account']['bank_name'],
                'fee' => $data['fee'],
            ],
        ];
    }

    /**
     * Prepare Kora Card Funding transaction data.
     */
    private function prepareKCardFunding(array $data)
    {
        return [
            'transactionId' => $data['transaction_reference'],
            'status' => ucfirst($data['status'] ?? 'pending'),
            'amount' => $data['amount'] ?? 0,
            'currencyCode' => $data['currency'] ?? 'USD',
            'recipientPhone' => null,
            'recipientEmail' => null,
            'customIdentifier' => $data['transaction_reference'] ?? null,
            'created_at' => date('Y-m-d H:i:s'),
            'exchangeRate' => $data['exchange_rate'] ?? null,
            'balanceUpdated' => $data['balance_updated'] ?? 0,
            'additionalData' => [
                'fee' => $data['fee'] ?? 0,
                'balance' => $data['balance'] ?? null, // new balance after funding
                'type' => $data['type'] ?? 'card_funding',
            ],
        ];
    }

    /**
     * Prepare Kora Card Creation transaction data.
     */
    private function prepareKCardCreation(array $data)
    {
        return [
            'transactionId' => $data['transactionId'] ?? null,
            'status' => ucfirst($data['status'] ?? 'pending'),
            'amount' => $data['amount'] ?? 0,
            'currencyCode' => $data['currency'] ?? 'USD',
            'recipientPhone' => null,
            'recipientEmail' => null,
            'customIdentifier' => $data['customIdentifier'] ?? null,
            'created_at' => isset($data['date'])
                ? $this->convertToDateTime($data['date'])
                : date('Y-m-d H:i:s'),
            'exchangeRate' => $data['exchange_rate'] ?? null,
            'balanceUpdated' => $data['balance_updated'] ?? 0,
            'additionalData' => [
                'type' => $data['type'] ?? 'card_creation',
                'last_four' => $data['last_four'] ?? null,
                'first_six' => $data['first_six'] ?? null,
            ],
        ];
    }

    /**
     * Prepare cryptocurrency transaction data.
     */
    private function prepareCryptoTransaction(array $data)
    {
        return [
            'transactionId' => $data['transactionId'],
            'status' => $data['status'],
            'amount' => $data['amount'],
            'currencyCode' => $data['currency'],
            'recipientPhone' => null,
            'recipientEmail' => $data['metadata']['buyerEmail'],
            'customIdentifier' => $data['metadata']['orderId'],
            'exchangeRate' => $data['exchangeRate'],
            'created_at' => $this->convertTimestampToDatetime($data['createdTime']),
            'additionalData' => [
                'checkoutLink' => $data['checkoutLink'],
                'paymentMethods' => $data['checkout']['paymentMethods'],
                'expirationMinutes' => $data['checkout']['expirationMinutes'],
                'monitoringExpiration' => $this->convertTimestampToDatetime($data['monitoringExpiration']),
            ]
        ];
    }

    /**
     * Converts a UNIX timestamp to a MySQL DATETIME string.
     *
     * @param int $timestamp The UNIX timestamp to convert.
     * @return string The formatted DATETIME string (e.g., 'YYYY-MM-DD HH:MM:SS').
     */
    public static function convertTimestampToDatetime(int $timestamp): string
    {
        return date('Y-m-d H:i:s', $timestamp);
    }

    /**
     * Converts ISO 8601 date to MySQL DateTime format (Y-m-d H:i:s).
     *
     * @param string $isoDate
     * @return string
     */
    private function convertToDateTime(string $isoDate): string
    {
        try {
            return (new \DateTime($isoDate))->format('Y-m-d H:i:s');
        } catch (Exception $e) {
            return date('Y-m-d H:i:s'); // Default to current time if conversion fails
        }
    }

    public function updateUserTransaction(int $id, array $data): bool
    {
        $fields = "";
        $params = [];

        foreach ($data as $key => $value) {
            $fields .= "$key = :$key, ";
            $params[":$key"] = $value;
        }

        $fields = rtrim($fields, ', ');

        $sql = "UPDATE transactions SET $fields WHERE id = :id AND user_id = :user_id";

        // Add the id and user_id to the params array
        $params[':id'] = $id;
        $params[':user_id'] = $this->user_id;

        $stmt = $this->dbConn->prepare($sql);

        // Bind all parameters
        foreach ($params as $key => &$value) {
            $stmt->bindParam($key, $value);
        }

        return $stmt->execute();
    }

    public function getAllUserTransactions()
    {
        $sql = "SELECT * FROM `transactions` WHERE `user_id` = :user_id ORDER BY `id` DESC";

        $stmt = $this->dbConn->prepare($sql);
        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllUTransc($FromRecordNum, $RecordsPerPage, $minAmount = null, $maxAmount = null)
    {
        // Start building the base query
        $query = "SELECT * FROM `transactions` WHERE `user_id` = :id";

        // Add conditions for min and max amount if provided
        if ($minAmount !== null && !empty($minAmount)) {
            $query .= " AND `amount` >= :minAmount";
        }

        if ($maxAmount !== null && !empty($maxAmount)) {
            $query .= " AND `amount` <= :maxAmount";
        }

        // Add order and limit to the query
        $query .= " ORDER BY `id` DESC LIMIT :sta1, :sta2";

        // Prepare the statement
        $stmt = $this->dbConn->prepare($query);

        // Bind the parameters
        $stmt->bindValue(':sta1', (int) $FromRecordNum, PDO::PARAM_INT);
        $stmt->bindValue(':sta2', (int) $RecordsPerPage, PDO::PARAM_INT);
        $stmt->bindParam(':id', $this->user_id);

        // Bind min and max amount if provided
        if ($minAmount !== null && !empty($minAmount)) {
            $stmt->bindValue(':minAmount', (float) $minAmount, PDO::PARAM_STR);
        }

        if ($maxAmount !== null && !empty($maxAmount)) {
            $stmt->bindValue(':maxAmount', (float) $maxAmount, PDO::PARAM_STR);
        }

        // Execute the query
        $stmt->execute();

        // Fetch and return the results
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTransactionUserByTransactionId($user_id, $id)
    {
        $stmt = $this->dbConn->prepare("SELECT * FROM `transactions` WHERE `user_id` = :user_id AND transactionId = :id ORDER BY `id` DESC");
        $stmt->bindValue(":user_id", $user_id, PDO::PARAM_INT);
        $stmt->bindValue(":id", $id, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getTransactionByCustomIdentifier($orderId)
    {
        $stmt = $this->dbConn->prepare("SELECT * FROM `transactions` WHERE `customIdentifier` = :id ORDER BY `id` DESC");
        $stmt->bindValue(":id", $orderId, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}