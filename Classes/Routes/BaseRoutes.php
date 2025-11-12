<?php
declare(strict_types=1);

namespace MyApp\Routes;

use MyApp\Enums\BaseAction;
use PH7\JustHttp\StatusCode;

abstract class BaseRoutes
{
    protected object $controller;

    public function __construct(object $controller)
    {
        $this->controller = $controller;
    }

    /**
     * Retrieves the request payload, supporting both application/json and form-urlencoded data.
     *
     * @return array The request payload as an associative array.
     */
    protected function getPayload(): array
    {
        // Start with the standard POST data.
        $payload = $_POST;

        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        if (empty($payload)) {
        // Check if the content type is JSON.
        if (str_contains($contentType, 'application/json')) {
            $rawInput = file_get_contents('php://input');

            // Check if there's raw input to decode.
            if (!empty($rawInput)) {
                $jsonData = json_decode($rawInput, true);

                // Check for JSON decoding errors.
                if (json_last_error() !== JSON_ERROR_NONE) {
                    // Return an error response immediately.
                    http_response_code(421); // Bad Request
                    echo json_encode(['message' => 'Invalid JSON data format']);
                    exit;
                }

                // Merge the JSON data into the payload, which effectively overrides $_POST data.
                $payload = array_merge($payload, $jsonData);
            }
        }
        // After processing, check if any data was provided at all.
        // if (empty($payload)) {
        //     http_response_code(421); // Bad Request
        //     echo json_encode(['message' => 'No data provided in request']);
        //     exit;
        // }
    }

        return $payload;
    }

    public function handleGet(?string $action, int|string|null $id)
    {
        $enumAction = BaseAction::tryFrom($action ?? '');
        // can make use of $_GET
        return match ($enumAction) {
            BaseAction::GET_ => $this->controller->get($action),
            BaseAction::GET => $this->controller->get($id), // status 200
            BaseAction::GET_ALL => $this->controller->getAll($id), // status 200
            default => $this->handleCustomGet($action, $id) // status 404
        };
    }

    public function handlePost(?string $action, int|string|null $id)
    {
        $payload = $this->getPayload();
 
        $enumAction = BaseAction::tryFrom($action);
        return match ($enumAction) {
            BaseAction::CREATE => $this->controller->create($payload, $id),
            default => $this->handleCustomPost($action, $payload),
        };
    }

    public function handlePut(?string $action, int|string|null $id)
    {
        $payload = $this->getPayload();
        $enumAction = BaseAction::tryFrom($action);
        return match ($enumAction) {
            BaseAction::UPDATE => $this->controller->update($payload, $id), // status 203
            default => $this->handleCustomPut($action, $payload), // status 404
        };
    }

    public function handleDelete(?string $action, int|string|null $id)
    {
        $payload = $this->getPayload();
        $enumAction = BaseAction::tryFrom($action);
        return match ($enumAction) {
            BaseAction::DELETE => $this->controller->delete($payload, $id), // status 204
            default => $this->handleCustomDelete($action, $payload),
        };
    }

    // 🔑 Force child routes to define these
    abstract protected function handleCustomGet(?string $action, int|string|null $id);
    abstract protected function handleCustomPost(?string $action, ?array $data);
    abstract protected function handleCustomPut(?string $action, ?array $data);
    abstract protected function handleCustomDelete(?string $action, ?array $data);

    // By default return error — child classes override as needed
    protected function handleFailedCustomGet(?string $action)
    {
        response(['status' => false, 'message' => "Invalid GET $action"], StatusCode::NOT_FOUND);
    }

    protected function handleFailedCustomPost(?string $action)
    {
        response(['status' => false, 'message' => "Invalid POST $action"], StatusCode::NOT_FOUND);
    }

    protected function handleFailedCustomPut(?string $action)
    {
        response(['status' => false, 'message' => "Invalid PUT $action"], StatusCode::NOT_FOUND);
    }

    protected function handleFailedCustomDelete(?string $action)
    {
        response(['status' => false, 'message' => "Invalid DELETE $action"], StatusCode::NOT_FOUND);
    }
}