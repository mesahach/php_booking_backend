<?php
use MyApp\Enums\BaseAction;
use PH7\JustHttp\StatusCode;
/**
 * Dispatch common CRUD actions to a controller.
 */
function handleAction(BaseAction $action, object $controller, int $id): array
{
    return match ($action) {
        BaseAction::CREATE => $controller->create(),
        BaseAction::GET => $controller->get($id),
        BaseAction::GET_ALL => $controller->getAll(),
        BaseAction::UPDATE => $controller->update($id),
        BaseAction::DELETE => $controller->delete($id),
    };
}

/**
 * Helper for 405 responses with Allow header.
 */
function methodNotAllowed(array $allowed): never
{
    header('Allow: ' . implode(', ', $allowed));
    response(['status' => false, 'message' => 'Method Not Allowed', 'allowed' => $allowed], StatusCode::METHOD_NOT_ALLOWED);
    exit;
}