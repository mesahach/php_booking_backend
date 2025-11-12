<?php 
declare(strict_types=1);
require_once __DIR__ . '/../../src/routeActions.php';
use PH7\JustHttp\StatusCode;
use MyApp\Validation\Exception\InvalidUserException;

$method = $_SERVER['REQUEST_METHOD'];
$paths = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = trim($paths, '/');
$path = explode('/', $path);

$basePathDepth = 0; // change this if project is inside deeper folder
$resource = $path[$basePathDepth] ?? null;
$action = $path[$basePathDepth + 1] ?? null;
$id = $path[$basePathDepth + 2] ?? 0; // for xampp?? $path[6]

// Check if resource exists
if ($resource) {
    $className = ucfirst(strtolower($resource)) . "Routes";
    $namespace = "MyApp\\Routes";
    $fullClassName = $namespace . "\\" . $className;

    if (class_exists($fullClassName)) {
        $routes = new $fullClassName();

        $methodMap = [
            'GET' => 'handleGet',
            'POST' => 'handlePost',
            'PUT' => 'handlePut',
            'DELETE' => 'handleDelete'
        ];

        if (
            isset($methodMap[$method]) &&
            method_exists($routes, $methodMap[$method])
        ) {
            try {
                $response = $routes->{$methodMap[$method]}($action, $id);
                response($response);
            } catch (InvalidUserException $e) {
                response([
                    'status' => false,
                    'message' => $e->getMessage(),
                    'code' => $e->getCode(),
                ]);
            }
             catch (\Exception $e) {
                response([
                    'status' => false,
                    'message' => $e->getMessage()
                ], StatusCode::INTERNAL_SERVER_ERROR);
            }
        } else {
            methodNotAllowed(allowed: array_keys($methodMap));
        }
    } else {
        response(['status' => false, 'message' => 'Resource not found'],StatusCode::NOT_FOUND);
    }
} else {
    response(['status' => false, 'message' => 'Invalid request'], StatusCode::BAD_REQUEST);
}