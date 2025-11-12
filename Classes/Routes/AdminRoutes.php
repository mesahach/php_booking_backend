<?php
namespace MyApp\Routes;
use MyApp\Controllers\AdminController;
use MyApp\Routes\BaseRoutes;
use MyApp\Enums\AdminActions;
use PH7\JustHttp\StatusCode;

class AdminRoutes extends BaseRoutes
{
    public function __construct()
    {
        parent::__construct(new AdminController());
    }

    protected function handleCustomGet(?string $action, string|int|null $id)
    {
        $enumAction = AdminActions::tryFrom($action);
        return match ($enumAction) {
            AdminActions::GET => $this->controller->get($id),
            AdminActions::GET_ALL => $this->controller->getAll($id),
            AdminActions::PROFILE => $this->controller->getProfile(),
            AdminActions::IS_LOGGED_IN => $this->controller->isLoggedIn(),
            AdminActions::COUNTDATA => $this->controller->countData(),
            default  => parent::handleFailedCustomGet($action),
        };
    }

    protected function handleCustomPost(?string $action, ?array $data)
    {
        if (!$action) {
            return response(['status' => false, 'message' => 'No action specified', 'code' => StatusCode::BAD_REQUEST]);
        }

        // Try to map action string to enum
        $actionEnum = AdminActions::tryFrom($action);

        return match ($actionEnum) {
            AdminActions::CREATE => $this->controller->create($data),
            AdminActions::GET => $this->controller->get($data),
            AdminActions::GET_ALL => $this->controller->getAll(),
            AdminActions::UPDATE => $this->controller->update($data),
            AdminActions::DELETE => $this->controller->delete($data),
            AdminActions::LOGIN => $this->controller->login($data),
            default => parent::handleFailedCustomPost($action),
        };
    }

    protected function handleCustomPut(?string $action, ?array $data)
    {
        $enumAction = AdminActions::tryFrom($action);
        return match ($enumAction) {
            AdminActions::UPDATE => $this->controller->update($data),
            default => parent::handleFailedCustomPut($action),
        };
    }

    protected function handleCustomDelete(?string $action, ?array $data)
    {
        $enumAction = AdminActions::tryFrom($action);
        return match ($enumAction) {
            AdminActions::DELETE => $this->controller->delete($data),
            AdminActions::LOGOUT => $this->controller->logout(),
            default => parent::handleFailedCustomDelete($action),
        };
    }
}
