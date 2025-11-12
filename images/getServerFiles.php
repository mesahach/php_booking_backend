<?php
require_once "../vendor/autoload.php";

$allowedExtensions = ['jpg', 'jpeg', 'png', 'pdf'];

$fileManager = new MyApp\Utils\FilePathManagerClass();
$moreDataFolder = $fileManager->getDataImageFolder();

// Build file path safely
$filename = basename($_GET['file'] ?? '');
$filePath = realpath("$moreDataFolder$filename");


if (!$filePath || !file_exists($filePath)) {
    http_response_code(404);
    die('File not found.');
}

// Optional: security check
$ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
if (!in_array($ext, $allowedExtensions)) {
    http_response_code(403);
    exit("Forbidden file type");
}

// Ensure the file is within the intended directory
if (strpos($filePath, realpath($moreDataFolder)) !== 0) {
    http_response_code(403);
    die('Access denied.');
}

// Serve the file
header('Content-Type: ' . mime_content_type($filePath));
header('Content-Length: ' . filesize($filePath));
readfile($filePath);
exit;