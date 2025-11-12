<?php
namespace MyApp\Config;

use MyApp\AllowedSiteConfig;
use MyApp\Utils\FilePathManagerClass;
use PH7\JustHttp\StatusCode;

// default site data
$siteData = [];

$origin = getOrigin();

$ObjDBMg = new AllowedSiteConfig();
$allowedOrigins = $ObjDBMg->allowedSites();

if ($origin == '') {
    if (isset($_SERVER['HTTP_X_API_KEY'])) {
        $apiKey = trim($_SERVER['HTTP_X_API_KEY']);
        $siteData = array_find($allowedOrigins, fn($site) => $site['apiKey'] === $apiKey);

        if (!$siteData) {
            response([
                'status' => false,
                'message' => 'Invalid Origin',
                'code' => StatusCode::UNAUTHORIZED
            ]);
        }

        $origin = $siteData['link'];
    } else {
        response([
            'status' => false,
            'message' => 'Invalid Origin',
            'code' => StatusCode::UNAUTHORIZED
        ]);
    }
} else {
    $siteData = array_find($allowedOrigins, fn($site) => $site['link'] === $origin);

    if (!$siteData) {
        response([
            'status' => false,
            'message' => 'Origin not allowed',
            'code' => StatusCode::UNAUTHORIZED
        ]);
    }

    $origin = $siteData['link'];
}

DEFINE('siteName', $siteData['name']);
DEFINE('siteDomain', $siteData['domain']);
// DEFINE('emailPass', $siteData['emailPass']);
DEFINE('siteLink', $siteData['link']);
DEFINE('supportMail', 'support@' . $siteData['domain']);
DEFINE('infoMail', 'info@' . $siteData['domain']);
DEFINE('noreplyMail', 'noreply@' . $siteData['domain']);
DEFINE('siteAddress', $siteData['address']);
DEFINE('sitePhone', $siteData['phone']);
DEFINE('serverDomain', $_ENV['SERVER_DOMAIN']);

$fileManager = new FilePathManagerClass();

$profileFolder = $fileManager->getProfileImageFolder();
$dataFolder = $fileManager->getDataImageFolder();

DEFINE('profileFolder', $profileFolder);
DEFINE('dataFolder', $dataFolder);
