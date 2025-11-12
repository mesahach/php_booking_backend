<?php
$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, 'https://www.' . $_SERVER['SERVER_NAME'] . '/api/deleteDataAPI_-01.php');
curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
$result = curl_exec($curl);

if (!$result) {
    echo "Could not get New Resource [Reload]";
}
curl_close($curl);
// this is how to install /usr/local/bin/php /home/user_name/public_html/file_name