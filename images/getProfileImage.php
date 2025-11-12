<?php
require_once "../vendor/autoload.php";

$allowedExtensions = ['jpg', 'jpeg', 'png', 'pdf']; // Allowed file types

$fileManager = new MyApp\Utils\FilePathManagerClass();

$profileFolder = $fileManager->getProfileImageFolder();

$filePath = realpath('/../' . $profileFolder . basename($_GET['file']));

if (!$filePath || !file_exists($filePath)) {
    http_response_code(404);
    die('File not found.');
}

// Ensure the file is within the intended directory
if (strpos($filePath, realpath(__DIR__ . '/../' . $profileFolder)) !== 0) {
    http_response_code(403);
    die('Access denied.');
}

// Serve the file
header('Content-Type: ' . mime_content_type($filePath));
header('Content-Length: ' . filesize($filePath));
readfile($filePath);
exit;