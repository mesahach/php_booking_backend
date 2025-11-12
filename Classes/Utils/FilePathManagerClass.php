<?php
namespace MyApp\Utils;

class FilePathManagerClass
{
    protected string $basePath;

    public function __construct()
    {
        $this->basePath = __DIR__ . '/../../uploads'; // Base uploads directory
        if (!is_dir($this->basePath)) {
            mkdir($this->basePath, 0755, true);
        }
    }

    protected function getDomainSuffix(): string
    {
        // Prefer Origin, fallback to Referer
        $origin = $_SERVER['HTTP_ORIGIN'] ?? ($_SERVER['HTTP_REFERER'] ?? 'default');

        // Extract domain
        $host = parse_url($origin, PHP_URL_HOST);

        if (!$host) {
            return 'default';
        }
        $domain = preg_replace('/^www\./', '', strtolower($host));
        return str_replace('.', '_', $domain); // e.g., "gomeloa_com"
    }

    protected function createFolderIfNotExists(string $folderName): string
    {
        $fullPath = $this->basePath . '/' . $folderName;
        if (!is_dir($fullPath)) {
            mkdir($fullPath, 0755, true);
        }
        return $fullPath;
    }

    public function getProfileImageFolder(): string
    {
        $folder = "profile_image_{$this->getDomainSuffix()}/";
        return $this->createFolderIfNotExists($folder);
    }

    public function getDataImageFolder(): string
    {
        $folder = "data_{$this->getDomainSuffix()}/";
        return $this->createFolderIfNotExists($folder);
    }

    public function getAllImagesInFolder(string $dataFolder): array
    {
        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'svg'];
        $images = [];

        // Ensure folder exists
        if (is_dir($dataFolder)) {
            // Use scandir to list all files
            $files = scandir($dataFolder);

            foreach ($files as $file) {
                // Skip . and ..
                if ($file === '.' || $file === '..') {
                    continue;
                }

                $filePath = $dataFolder . DIRECTORY_SEPARATOR . $file;

                // Check if it's a file and has a valid image extension
                if (is_file($filePath)) {
                    $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
                    if (in_array($extension, $imageExtensions)) {
                        $images[] = $file;
                    }
                }
            }
        }

        return $images;
    }

}