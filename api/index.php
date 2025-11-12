<?php
require __DIR__ . '/../vendor/autoload.php';

use PH7\PhpHttpResponseHeader\Http;
Http::setContentType('application/json');

// run php -S localhost:8000 api in the project root to run the api

use Whoops\Run as WhoopsRun;
use Whoops\Handler\JsonResponseHandler;

// Handles all exceptions
$whoops = new WhoopsRun;
// $whoops->allowQuit(true);
$whoops->writeToOutput(true);
$whoops->pushHandler(new JsonResponseHandler);
$whoops->register();

// (new MyApp\AllowCors("https://localhost"))->init();

// if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
//     http_response_code(204); // Indicate preflight success
//     exit; // Stop further processing for OPTIONS
// }
require __DIR__ . '/../Classes/Config/config.php';
require __DIR__ . '/../src/functions.php';
require __DIR__ . '/../Classes/Config/site_config.php';
require __DIR__ . '/../src/header.php';
require __DIR__ . '/../Classes/Config/database_config.php';
require __DIR__ . '/routes/routes.php';