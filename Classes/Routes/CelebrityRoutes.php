<?php
namespace MyApp\Routes;
use MyApp\Controllers\CelebrityController;
use MyApp\Routes\BaseRoutes;
use MyApp\Enums\CelebrityActions;
use PH7\JustHttp\StatusCode;

class CelebrityRoutes extends BaseRoutes
{
    public function __construct()
    {
        parent::__construct(new CelebrityController());
    }

    protected function handleCustomGet(?string $action, string|int|null $id)
    {
        $enumAction = CelebrityActions::tryFrom($action);
        return match ($enumAction) {
            CelebrityActions::GET => $this->controller->get($id),
            CelebrityActions::GET_ALL => $this->controller->getAll($id),
            default => parent::handleFailedCustomGet($action),
        };
    }

    protected function handleCustomPost(?string $action, ?array $data)
    {
        if (!$action) {
            return response(['status' => false, 'message' => 'No action specified', 'code' => StatusCode::BAD_REQUEST]);
        }

        // Try to map action string to enum
        $actionEnum = CelebrityActions::tryFrom($action);

        return match ($actionEnum) {
            CelebrityActions::CREATE => $this->controller->create($data),
            CelebrityActions::GET => $this->controller->get($data),
            CelebrityActions::GET_ALL => $this->controller->getAll(),
            CelebrityActions::UPDATE => $this->controller->update($data),
            CelebrityActions::DELETE => $this->controller->delete($data),
            default => parent::handleFailedCustomPost($action),
        };
    }

    protected function handleCustomPut(?string $action, ?array $data)
    {
        $enumAction = CelebrityActions::tryFrom($action);
        return match ($enumAction) {
            CelebrityActions::UPDATE => $this->controller->update($data),
            default => parent::handleFailedCustomPut($action),
        };
    }

    protected function handleCustomDelete(?string $action, ?array $data)
    {
        $enumAction = CelebrityActions::tryFrom($action);
        return match ($enumAction) {
            CelebrityActions::DELETE => $this->controller->delete($data),
            default => parent::handleFailedCustomDelete($action),
        };
    }
}
