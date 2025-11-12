<?php
// getAllImages.php
require_once "../vendor/autoload.php";

$allowedExtensions = ['jpg', 'jpeg', 'png']; // Only images
$fileManager = new MyApp\Utils\FilePathManagerClass();
$imageFolder = $fileManager->getDataImageFolder();

// Validate folder exists
if (!is_dir($imageFolder)) {
    http_response_code(500);
    echo json_encode(['error' => 'Image directory not found.']);
    exit;
}

$images = [];
$files = scandir($imageFolder);

foreach ($files as $file) {
    if ($file === '.' || $file === '..')
        continue;

    $filePath = realpath("$imageFolder/$file");
    $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

    // Security: must be in folder + allowed type
    if (
        strpos($filePath, realpath($imageFolder)) === 0 &&
        in_array($ext, $allowedExtensions) &&
        is_file($filePath)
    ) {
        $images[] = [
            'name' => $file,
            'url' => 'getServerFiles.php?file=' . urlencode($file),
            'size' => round(filesize($filePath) / 1024, 1) . ' KB'
        ];
    }
}

// Return JSON
header('Content-Type: application/json');
echo json_encode($images);
exit;