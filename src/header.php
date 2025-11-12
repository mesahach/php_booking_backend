<?php 
use PH7\PhpHttpResponseHeader\Http;
(new MyApp\AllowCors(siteLink))->init();

Http::setContentType('application/json');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204); // Indicate preflight success
    exit; // Stop further processing for OPTIONS
}