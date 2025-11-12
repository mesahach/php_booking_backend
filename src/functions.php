<?php

use \PH7\PhpHttpResponseHeader\Http;
use PH7\JustHttp\StatusCode;

function responseOld(array $data, int $code = 412)
{
    $data['status'] ??= false;
    $failed = $data['status'] ? 200: false;
    Http::SetHeadersByCode($data['code'] ?? $failed ?? $code ?? StatusCode::PRECONDITION_FAILED);
    echo json_encode($data);
    exit;
}

function response(array $data, int $code = 412)
{
    // Default response structure
    $dataHere = [
        'status' => false,
        'message' => '',
        'data' => null,
        'code' => $code ?? StatusCode::PRECONDITION_FAILED
    ];

    // Merge defaults with user-provided values
    $dataHere = array_merge($dataHere, $data);

    // Ensure HTTP response code is properly set
    Http::SetHeadersByCode($dataHere['code']);

    // Pretty print only if "data" is an array
    $options = JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE;
    if (is_array($dataHere['data'])) {
        $options |= JSON_PRETTY_PRINT;
    }
    if ($dataHere['data'] === null) {
        unset($dataHere['data']);
    }

    echo json_encode($dataHere, $options);
    exit;
}


function validateString($string, $filter = FILTER_SANITIZE_SPECIAL_CHARS)
{
    // Trim unnecessary whitespace
    $string = trim($string);

    // Remove slashes once (redundant to call both stripslashes and stripcslashes)
    $string = stripslashes($string);

    // Apply selected sanitization filter
    $data = htmlspecialchars(filter_var($string, $filter));

    // Decode HTML entities
    $data = html_entity_decode($data, ENT_QUOTES, 'UTF-8');

    // Return the sanitized and decoded string
    return $data;
}

function generateAccountNumber($accountType)
{
    // Define prefixes for each account type
    $prefixes = [
        "Checking Account" => "01",
        "Savings Account" => "02",
        "Fixed Deposit Account" => "03",
        "Current Account" => "04",
        "Business Account" => "05",
        "Non Resident Account" => "06",
        "Cooperate Business Account" => "07",
        "Investment Account" => "08"
    ];

    // Check if the account type exists in the prefixes array
    if (!array_key_exists($accountType, $prefixes)) {
        throw new InvalidArgumentException("Invalid account type provided.");
    }

    // Get the prefix for the given account type
    $prefix = $prefixes[$accountType];

    // Generate a unique 10-digit number (excluding the prefix)
    $uniqueNumber = str_pad(string: rand(min: 0, max: 999999999), length: 8, pad_string: '0', pad_type: STR_PAD_LEFT);

    // Combine prefix and unique number to create the account number
    $accountNumber = "$prefix$uniqueNumber";

    return $accountNumber;
}

function getOrigin(): string
{
    $origin = '';

    // 1. Check normal Origin header
    if (isset($_SERVER['HTTP_ORIGIN'])) {
        $origin = $_SERVER['HTTP_ORIGIN'];
    }

    // 2. If not set, check Referer header
    if (empty($origin) && isset($_SERVER['HTTP_REFERER'])) {
        $referer = $_SERVER['HTTP_REFERER'];
        $parsedReferer = parse_url($referer);
        if (isset($parsedReferer['scheme'], $parsedReferer['host'])) {
            $origin = $parsedReferer['scheme'] . '://' . $parsedReferer['host'];
            if (isset($parsedReferer['port'])) {
                $origin .= ':' . $parsedReferer['port'];
            }
        }
    }

    // 3. As last fallback, check custom X-Request-Origin header
    if (empty($origin) && isset($_SERVER['HTTP_X_REQUEST_ORIGIN'])) {
        $origin = $_SERVER['HTTP_X_REQUEST_ORIGIN'];
    }

    // Normalize
    $origin = rtrim(strtolower(trim($origin)), '/');
    return $origin;
}


function validateNumber($id)
{
    // Remove any non-numeric characters from the input
    $cleaned_id = preg_replace('/[^0-9]/', '', $id);

    // Ensure the cleaned ID is not empty
    if (empty($cleaned_id)) {
        return false;
    }

    // Return the cleaned ID as an integer
    return $cleaned_id;
}

function validateId($id)
{
    if ($id == 0 || $id == 1) {
        return $id;
    }

    // Ensure $id is numeric after sanitization and return it if so; otherwise, return false
    $sanitized_id = filter_var(trim($id), FILTER_SANITIZE_NUMBER_INT);
    return is_numeric($sanitized_id) ? (int) $sanitized_id : false;
}

function array_find(array $array, callable $callback): ?array
{
    foreach ($array as $item) {
        if ($callback($item)) {
            return $item;
        }
    }
    return null;
}


function projectOrder($category)
{
    $order = '';
    $order = match ($category) {
        'Authentication Page' => '1',
        'Dashboard Page' => '2, 4',
        'Front Page' => '3, 4',
        'More Pages' => '4, 4',
        default => '',
    };
    return $order;
}

function validateEmail($ema)
{
    // Trim and sanitize input to remove any unwanted characters
    $eml = trim($ema);
    $emai = filter_var($eml, FILTER_SANITIZE_EMAIL);

    // Validate email address
    if (filter_var($emai, FILTER_VALIDATE_EMAIL)) {
        // Additional regex check for better validation (optional but recommended)
        $emailPattern = "/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/";
        if (preg_match($emailPattern, $emai)) {
            return $emai;
        }
    }

    // Return false if validation fails
    return false;
}

function possible_combinations_function($arrays)
{
    $result = [[]];

    foreach ($arrays as $key => $values) {
        $append = [];
        foreach ($result as $product) {
            foreach ($values as $item) {
                $product_combination = $product;
                $product_combination[$key] = $item;
                $append[] = $product_combination;
            }
        }
        $result = $append;
    }

    return $result;
}

function generateInvoiceNumber($user_id)
{
    $user_id_length = strlen($user_id);
    $required_digits = ($user_id_length < 5) ? 13 : 10;

    // Generate a random number with the required number of digits
    $random = mt_rand(pow(10, $required_digits - 1), pow(10, $required_digits) - 1);

    // Combine user ID as prefix and random number
    $invoiceNumber = $user_id . $random;

    return $invoiceNumber;
}

function dateTime()
{
    $createdOn = date('d-M-Y h:i:s a', time());
    return $createdOn;
}

function handleFileUploadError(int $errorCode): string
{
    $errorMessages = [
        UPLOAD_ERR_PARTIAL => "File only partially uploaded",
        UPLOAD_ERR_NO_FILE => "No file was uploaded",
        UPLOAD_ERR_EXTENSION => "File upload stopped by a PHP extension",
        UPLOAD_ERR_FORM_SIZE => "File exceeds MAX_FILE_SIZE in the form",
        UPLOAD_ERR_INI_SIZE => "File exceeds upload max filesize in PHP.ini",
        UPLOAD_ERR_NO_TMP_DIR => "Temporary folder is not found",
        UPLOAD_ERR_CANT_WRITE => "Failed to write file",
    ];
    // use case handleFileUploadError($_FILES['filename']['error'])
    return $errorMessages[$errorCode] ?? 'Unknown upload error occurred';
}

function uploadFile(string $targetDir, string $fieldName, array $allowedTypes = []): array
{
    // Check for uploaded file
    if (!isset($_FILES[$fieldName]) || $_FILES[$fieldName]['error'] !== UPLOAD_ERR_OK) {
        $message = handleFileUploadError($_FILES[$fieldName]['error'] ?? UPLOAD_ERR_NO_FILE); // Handle missing file
        return ['status' => false, 'message' => $message, 'filename' => ''];
    }

    // Get file information
    $fileInfo = $_FILES[$fieldName];
    $tmpName = $fileInfo['tmp_name'];
    $size = $fileInfo['size'];
    $mimeType = (new finfo(FILEINFO_MIME_TYPE))->file($tmpName);

    // Security checks
    if (!is_uploaded_file($tmpName)) {
        return ['status' => false, 'message' => 'Invalid uploaded file.', 'filename' => ''];
    }

    // Validate file size
    if ($size > 5767168) { // Adjust max size as needed (5.5MB)
        return ['status' => false, 'message' => 'File too large (max 5.5MB)', 'filename' => ''];
    }

    if (empty($allowedTypes)) {
        $allowedTypes = ['image/png', 'image/jpg', 'image/jpeg'];
    }

    // Validate file type (if allowedTypes provided)
    if ($allowedTypes && !in_array($mimeType, $allowedTypes)) {
        $allowedTypesStr = implode(', ', $allowedTypes);
        return ['status' => false, 'message' => "The file format is not allowed (only $allowedTypesStr are accepted)", 'filename' => ''];
    }

    // Generate unique filename
    $newFileName = date('dmYHis') . str_replace([" ", '/', ':', '@', '^', '-'], "", basename($fileInfo['name']));
    $targetFile = "$targetDir$newFileName";
    $i = 1;
    while (file_exists($targetFile)) {
        $newFileName = "($i)$newFileName";
        $targetFile = "$targetDir$newFileName";
        $i++;
    }

    // Move uploaded file
    if (move_uploaded_file($tmpName, $targetFile)) {
        return ['status' => true, 'message' => "File was successfully uploaded", 'filename' => $newFileName];
    } else {
        return ['status' => false, 'message' => 'Failed to move uploaded file.', 'filename' => ''];
    }
}

function uploadFileForMultiple(string $targetDir, string $fieldName, int $index = 0, array $allowedTypes = []): array
{
    // Detect if it's a multi-file upload
    if (is_array($_FILES[$fieldName]['name'])) {
        // Multiple files (images[])
        $fileInfo = [
            'name' => $_FILES[$fieldName]['name'][$index],
            'type' => $_FILES[$fieldName]['type'][$index],
            'tmp_name' => $_FILES[$fieldName]['tmp_name'][$index],
            'error' => $_FILES[$fieldName]['error'][$index],
            'size' => $_FILES[$fieldName]['size'][$index],
        ];
    } else {
        // Single file (image)
        $fileInfo = [
            'name' => $_FILES[$fieldName]['name'],
            'type' => $_FILES[$fieldName]['type'],
            'tmp_name' => $_FILES[$fieldName]['tmp_name'],
            'error' => $_FILES[$fieldName]['error'],
            'size' => $_FILES[$fieldName]['size'],
        ];
    }


    if ($fileInfo['error'] !== UPLOAD_ERR_OK) {
        $message = handleFileUploadError($fileInfo['error']);
        return ['status' => false, 'message' => $message, 'filename' => ''];
    }

    $tmpName = $fileInfo['tmp_name'];
    $size = $fileInfo['size'];
    $mimeType = (new finfo(FILEINFO_MIME_TYPE))->file($tmpName);

    if (!is_uploaded_file($tmpName)) {
        return ['status' => false, 'message' => 'Invalid uploaded file.', 'filename' => ''];
    }

    if ($size > 5767168) {
        return ['status' => false, 'message' => 'File too large (max 5.5MB)', 'filename' => ''];
    }

    if (empty($allowedTypes)) {
        $allowedTypes = ['image/png', 'image/jpg', 'image/jpeg'];
    }

    if (!in_array($mimeType, $allowedTypes)) {
        $allowedTypesStr = implode(', ', $allowedTypes);
        return ['status' => false, 'message' => "Invalid format (only $allowedTypesStr allowed)", 'filename' => ''];
    }

    $safeName = preg_replace('/[^A-Za-z0-9_\.-]/', '', basename($fileInfo['name']));
    $newFileName = date('YmdHis') . "_$safeName";
    $targetFile = "$targetDir$newFileName";

    if (move_uploaded_file($tmpName, $targetFile)) {
        return ['status' => true, 'message' => "File uploaded", 'filename' => $newFileName];
    } else {
        return ['status' => false, 'message' => 'Failed to move uploaded file.', 'filename' => ''];
    }
}

function id()
{
    $id = (int) $_GET['id'];
    if (isset($id)) {
        if ($id !== 0) {
            return $id;
        } else {
            return FALSE;
        }
    } else {
        return FALSE;
    }
}

function pin()
{
    $id = (int) $_GET['pin'];
    if (isset($id)) {
        if ($id !== 0) {
            return $id;
        } else {
            return FALSE;
        }
    } else {
        return FALSE;
    }
}

function getEmail()
{
    if (isset($_GET['email'])) {
        $email = urldecode($_GET['email']);
        return $email;
    } else {
        return FALSE;
    }
}

function getActNum()
{
    if (isset($_GET['act_num'])) {
        $email = urldecode($_GET['act_num']);
        return $email;
    } else {
        return FALSE;
    }
}

function currency($currency)
{
    if ($currency == "USD") {
        return "$";
    } elseif ($currency == "EUR") {
        return "€";
    } elseif ($currency == "GBP") {
        return '£';
    } elseif ($currency == "YEN") {
        return '¥';
    } else {
        return $currency;
    }
}

function userName($name)
{
    $pattern = '/^[A-Za-z_-][A-Za-z0-9_-]{5,31}$/';
    if (preg_match($pattern, $name)) {
        return $name;
    } else {
        return FALSE;
    }
}

function validatePassword($password)
{
    if (preg_match('/^(?=.*\d)(?=.*[@#\-,_$%^&+=§!\?])(?=.*[a-z])(?=.*[A-Z])[0-9A-Za-z@#\-,_$%^&+=§!\?]{8,20}$/', $password)) {
        return true;
    } else {
        return false;
    }
}

function v_password($password)
{
    if (preg_match("#.*^(?=.{8,20})(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*\W).*$#", $password)) {
        return true;
    } else {
        return false;
    }
}

function v_password2($password)
{
    if (preg_match("#.*^(?=.{8,20})(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9]).*$#", $password)) {
        return true;
    } else {
        return false;
    }
}

function v_password3($password)
{
    // At least 8-20 chars, at least one lowercase, one uppercase, and at least one number OR special character
    if (
        preg_match(
            '/^(?=.*[a-z])(?=.*[A-Z])(?=.*[\d\W]).{8,20}$/',
            $password
        )
    ) {
        return true;
    } else {
        return false;
    }
}

function truncate($string, $length, $stopanywhere = false)
{
    //truncates a string to a certain char length, stopping on a word if not specified otherwise.
    if (strlen($string) > $length) {
        //limit hit!
        $string = substr($string, 0, ($length - 3));
        if ($stopanywhere) {
            //stop anywhere
            $string .= '...';
        } else {
            //stop on a word.
            $string = substr($string, 0, strrpos($string, ' ')) . '...';
        }
    }
    return $string;
}

function truncate2($string, $length, $stopanywhere = false)
{
    //truncates a string to a certain char length, stopping on a word if not specified otherwise.
    if (strlen($string) > $length) {
        //limit hit!
        $string = substr($string, 0, ($length - 3));
        if ($stopanywhere) {
            //stop anywhere
            $string .= '**';
        } else {
            //stop on a word.
            $string = substr($string, 0, strrpos($string, ' ')) . '***';
        }
    }
    return $string;
}

function getIPAddress()
{
    //whether ip is from the share internet  
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    }
    //whether ip is from the proxy  
    elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }

    //whether ip is from the remote address  
    else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;

}

function escape($full)
{
    $fullname = '';
    for ($i = 0; $i < strlen($full); ++$i) {
        $char = $full[$i];
        $ord = ord($char);
        if ($char !== "'" && $char !== "\"" && $char !== '\\' && $ord >= 32 && $ord <= 126)
            $fullname .= $char;
        else
            $fullname .= '\\x' . dechex($ord);
    }
    return $fullname;
}

function currencySym($currency)
{
    return match ($currency) {
        "USD" => "$",
        "EUR" => "€",
        "GBP" => '£',
        "YEN" => '¥',
        default => $currency,
    };
}

function sumAmount($allData)
{
    $sum = 0;

    foreach ($allData as $data) {
        $sum = $sum + v_price($data['amount']);
    }
    return $sum;
}

function sumAllData($allData, $content)
{
    $sum = 0;

    foreach ($allData as $data) {
        $sum += v_price($data[$content]);
    }
    return number_format($sum, 2);
}

function sumAllU($allData)
{
    $sum = 0;

    foreach ($allData as $data) {
        $sum += v_price($data['deposit']);
    }
    return $sum;
}

function calAutoProfit($day, $profit, $endProfit)
{
    $data = 0;
    if ($day == 1) {
        $data = $endProfit / 6.66;
    } elseif ($day == 2) {
        $data = $endProfit / 5.667;
    } elseif ($day == 3) {
        $data = $endProfit / 8.667;
    } elseif ($day == 4) {
        $data = $endProfit / 6.667;
    } elseif ($day == 5) {
        $data = $endProfit / 7.66;
    } elseif ($day == 6) {
        $data = $endProfit / 4.536;
    } elseif ($day == 7 && $profit < $endProfit) {
        $data = $endProfit - $profit;
    }
    return $data;
}

function dayCount($edit)
{
    $date1 = date_create($edit);
    $date2 = date_create(date('Y-m-d'));
    $diff = date_diff($date1, $date2);
    return $diff->format("%r%a");
}

function newDayCount($edit)
{
    $origin = new DateTime($edit);
    $target = new DateTime(date('d-m-Y'));

    $interval = $origin->diff($target);
    return $interval->format('%r%a');
}

function jsonReport($code, $message)
{
    http_response_code($code);
    echo json_encode(['message' => $message, 'code' => $code]);
}
function getUserLocation()
{
    $ip = $_SERVER['REMOTE_ADDR'];
    $api_url = "https://ipinfo.io/{$ip}/json";

    // Make a GET request to the API
    $response = file_get_contents($api_url);

    // Decode the JSON response
    $location_data = json_decode($response, true);

    // Extract relevant information
    $city = $location_data['city'] ?? 'Unknown';
    $region = $location_data['region'] ?? 'Unknown';
    $country = $location_data['country'] ?? 'Unknown';

    // Return the location information
    return "City: $city, Region: $region, Country: $country";
}

function BTCprice($amount)
{
    $url = 'https://bitpay.com/api/rates';
    $json = json_decode(file_get_contents($url));
    $dollar = $btc = 0;

    foreach ($json as $obj) {
        if ($obj->code == 'USD')
            $btc = $obj->rate;
    }

    // echo "1 bitcoin=\$" . $btc . "USD<br />";
    $dollar = 1 / $btc;
    return round($dollar * $amount, 8);
}

function BTCpriceCurrency($amount, $currency)
{
    $url = 'https://bitpay.com/api/rates';
    $json = json_decode(file_get_contents($url));
    $dollar = $btc = 0;

    foreach ($json as $obj) {
        if ($obj->code == (string) $currency)
            $btc = $obj->rate;
    }

    // echo "1 bitcoin=\$" . $btc . "USD<br />";
    $dollar = 1 / $btc;
    return round($dollar * $amount, 8);
}

function getCoinPrice($currency, $coin)
{
    $ch = curl_init();
    curl_setopt_array(
        $ch,
        [
            CURLOPT_URL => 'https://bitpay.com/api/rates/' . $coin . '/' . $currency,
            CURLOPT_RETURNTRANSFER => true,
        ]
    );
    $response = curl_exec($ch);
    curl_close($ch);
    $data = json_decode($response, JSON_OBJECT_AS_ARRAY);
    return $data['rate'];
}

function currentBTC()
{
    return '
    <script>
        setInterval(function () {
            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function () {
                if (this.readyState == 4 && this.status == 200) {
                    var obj = JSON.parse(xhttp.responseText);
                    var nextobj = JSON.stringify(obj.USD);
                    var newobj = JSON.parse(nextobj);
                    document.getElementById("priceupdate").innerHTML = newobj.last;
                }
            };
            xhttp.open("GET", "https://blockchain.info/ticker", true);
            xhttp.send();
        }, 500);
    </script><span id="priceupdate"></span>
    ';
}

function checkStatus($data)
{
    return match ($data) {
        "ACTIVE" => "green",
        default => "red",
    };
}

function getCode($len)
{
    if ($len) {
        //define character libraries - remove ambiguous characters like iIl|1 0oO
        $sets = [];
        $sets[] = 'abcdefghijklmnopqrstuvwxyz';
        $sets[] = '1234567890';

        $wallet = '';

        //append a character from each set - gets first 4 characters
        foreach ($sets as $set) {
            $wallet .= $set[array_rand(str_split($set))];
        }

        //use all characters to fill up to $len
        while (strlen($wallet) < $len) {
            //get a random set
            $randomSet = $sets[array_rand($sets)];

            //add a random char from the random set
            $wallet .= $randomSet[array_rand(str_split($randomSet))];
        }

        //shuffle the wallet string before returning!
        return str_shuffle($wallet);

    }
}
// showclienttime();
#http://www.php.net/manual/en/timezones.php List of Time Zones

// echo site_time_ago('2016-03-11 04:58:00');  
function site_time_ago($timestamp)
{
    $time_ago = strtotime($timestamp);
    $current_time = time();
    $time_difference = $current_time - $time_ago;
    $seconds = $time_difference;
    $minutes = round($seconds / 60); // value 60 is seconds  
    $hours = round($seconds / 3600); //value 3600 is 60 minutes * 60 sec  
    $days = round($seconds / 86400); //86400 = 24 * 60 * 60;  
    $weeks = round($seconds / 604800); // 7*24*60*60;  
    $months = round($seconds / 2629440); //((365+365+365+365+366)/5/12)*24*60*60  
    $years = round($seconds / 31553280); //(365+365+365+365+366)/5 * 24 * 60 * 60  
    if ($seconds <= 60) {
        return "Just Now";
    } else if ($minutes <= 60) {
        return match ($minutes) {
            1 => "one minute ago",
            default => "$minutes minutes ago",
        };
    } else if ($hours <= 24) {
        return match ($hours) {
            1 => "an hour ago",
            default => "$hours hrs ago",
        };
    } else if ($days <= 7) {
        return match ($days) {
            1 => "yesterday",
            default => "$days days ago",
        };
    } else if ($weeks <= 4.3) //4.3 == 52/12  
    {
        return match ($weeks) {
            1 => "a week ago",
            default => "$weeks weeks ago",
        };
    } else if ($months <= 12) {
        return match ($months) {
            1 => "a month ago",
            default => "$months months ago",
        };
    } else {
        return match ($years) {
            1 => "one year ago",
            default => "$years years ago",
        };
    }
}

function dateConverter($date, $locale = "br")
{

    # Exception
    if (is_null($date))
        $date = date("m-d-Y H:i:s");

    # Let's go ahead and get a string date in case we've
    # been given a Unix Time Stamp
    if ($locale == "unix")
        $date = date("m-d-Y H:i:s", $date);

    # Separate Date from Time
    $date = explode(" ", $date);

    if ($locale == "br") {
        # Separate d/m/Y from Date
        $date[0] = explode("-", $date[0]);
        # Rearrange Date into m/d/Y
        $date[0] = $date[0][1] . "-" . $date[0][0] . "-" . $date[0][2];
    }

    # Return date in all formats
    # US
    $Return["datetime"]["us"] = implode(" ", $date);
    $Return["date"]["us"] = $date[0];

    # Universal
    $Return["time"] = $date[1];
    $Return["unix_datetime"] = strtotime($Return["datetime"]["us"]);
    $Return["unix_date"] = strtotime($Return["date"]["us"]);
    $Return["getdate"] = getdate($Return["unix_datetime"]);

    # BR
    $Return["datetime"]["br"] = date("d-b-Y H:i:s", $Return["unix_datetime"]);
    $Return["date"]["br"] = date("d-b-Y", $Return["unix_date"]);

    # Return
    return $Return;
}

function getOS()
{

    global $user_agent;

    $os_platform = "Unknown OS Platform";

    $os_array = ['/windows nt 10/i' => 'Windows 10', '/windows nt 6.3/i' => 'Windows 8.1', '/windows nt 6.2/i' => 'Windows 8', '/windows nt 6.1/i' => 'Windows 7', '/windows nt 6.0/i' => 'Windows Vista', '/windows nt 5.2/i' => 'Windows Server 2003/XP x64', '/windows nt 5.1/i' => 'Windows XP', '/windows xp/i' => 'Windows XP', '/windows nt 5.0/i' => 'Windows 2000', '/windows me/i' => 'Windows ME', '/win98/i' => 'Windows 98', '/win95/i' => 'Windows 95', '/win16/i' => 'Windows 3.11', '/macintosh|mac os x/i' => 'Mac OS X', '/mac_powerpc/i' => 'Mac OS 9', '/linux/i' => 'Linux', '/ubuntu/i' => 'Ubuntu', '/iphone/i' => 'iPhone', '/ipod/i' => 'iPod', '/ipad/i' => 'iPad', '/android/i' => 'Android', '/blackberry/i' => 'BlackBerry', '/webos/i' => 'Mobile',];

    foreach ($os_array as $regex => $value)
        if (preg_match($regex, $user_agent))
            $os_platform = $value;

    return $os_platform;
}

function getBrowser()
{

    global $user_agent;

    $browser = "Unknown Browser";

    $browser_array = [
        '/msie/i' => 'Internet Explorer',
        '/firefox/i' => 'Firefox',
        '/safari/i' => 'Safari',
        '/chrome/i' => 'Chrome',
        '/edge/i' => 'Edge',
        '/opera/i' => 'Opera',
        '/netscape/i' => 'Netscape',
        '/maxthon/i' => 'Maxthon',
        '/konqueror/i' => 'Konqueror',
        '/mobile/i' => 'Handheld Browser'
    ];

    foreach ($browser_array as $regex => $value)
        if (preg_match($regex, $user_agent))
            $browser = $value;

    return $browser;
}

function v_phone(string $telephone, int $minDigits = 9, int $maxDigits = 14)
{
    if (preg_match('/^[+][0-9]/', $telephone)) {
        //is the first character + followed by a digit
        $count = 1;
        $telephone = str_replace(['+'], '', $telephone, $count); //remove +
    }

    //remove white space, dots, hyphens and brackets
    $telephone = str_replace([' ', '.', '-', '(', ')'], '', $telephone);

    //are we left with digits only?
    if (is_numeric($telephone)) {
        return $telephone;
    } else {
        return FALSE;
    }

}

function cleanPhoneNum(string $telephone): string
{
    //remove white space, dots, hyphens and brackets
    $telephone = str_replace([' ', '.', '-', '(', ')'], '', $telephone);
    return $telephone;
}

function v_price(string $money): string
{
    //remove white space, dots, hyphens and brackets
    $telephone = str_replace([' ', ',', '(', ')', '₦', '$'], '', $money);
    if ($telephone > 0.001) {
        return $telephone;
    } else {
        return 0;
    }
}

function cancel()
{
    $_SESSION['amount'] = FALSE;
    $_SESSION['method'] = FALSE;
    $_SESSION['message'] = FALSE;
    $_SESSION['transStatus'] = FALSE;
}

function cancelTrade()
{
    $_SESSION['type'] = FALSE;
    $_SESSION['asset'] = FALSE;
    $_SESSION['timeT'] = FALSE;
    $_SESSION['amount'] = FALSE;
    $_SESSION['profit'] = FALSE;
    $_SESSION['actType'] = FALSE;

}

function randFloat()
{
    return mt_rand(1, 50) / 100000;
}

function getEthPrice()
{
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_POST, 1);
    // curl_setopt($curl, CURLOPT_POSTFIELDS, $auth_data);

    curl_setopt($curl, CURLOPT_URL, 'https://cex.io/api/last_price/ETH/USDT');
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    $result = curl_exec($curl);
    if (!$result) {
        return FALSE;
    } else {
        $data = json_decode($result);
        return $data->lprice;
    }
    curl_close($curl);
}