<?php
namespace MyApp\Routes;
use MyApp\Controllers\ReviewController;
use MyApp\Routes\BaseRoutes;
use MyApp\Enums\ReviewActions;
use PH7\JustHttp\StatusCode;

class ReviewRoutes extends BaseRoutes
{
    public function __construct()
    {
        parent::__construct(new ReviewController());
    }

    protected function handleCustomGet(?string $action, string|int|null $id)
    {
        $enumAction = ReviewActions::tryFrom($action);
        return match ($enumAction) {
            ReviewActions::GET => $this->controller->get($id),
            ReviewActions::GET_ALL => $this->controller->getAll(),
            default => parent::handleFailedCustomGet($action),
        };
    }

    protected function handleCustomPost(?string $action, ?array $data)
    {
        if (!$action) {
            return response(['status' => false, 'message' => 'No action specified', 'code' => StatusCode::BAD_REQUEST]);
        }

        // Try to map action string to enum
        $actionEnum = ReviewActions::tryFrom($action);

        return match ($actionEnum) {
            ReviewActions::CREATE => $this->controller->create($data),
            default => parent::handleFailedCustomPost($action),
        };
    }

    protected function handleCustomPut(?string $action, ?array $data)
    {
        $enumAction = ReviewActions::tryFrom($action);
        return match ($enumAction) {
            ReviewActions::UPDATE => $this->controller->update($data),
            default => parent::handleFailedCustomPut($action),
        };
    }

    protected function handleCustomDelete(?string $action, ?array $data)
    {
        $enumAction = ReviewActions::tryFrom($action);
        return match ($enumAction) {
            ReviewActions::DELETE => $this->controller->delete($data),
            default => parent::handleFailedCustomDelete($action),
        };
    }
}
