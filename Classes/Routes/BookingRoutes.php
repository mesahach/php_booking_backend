<?php
namespace MyApp\Routes;
use MyApp\Controllers\BookingController;
use MyApp\Routes\BaseRoutes;
use MyApp\Enums\BookingActions;
use PH7\JustHttp\StatusCode;

class BookingRoutes extends BaseRoutes
{
    public function __construct()
    {
        parent::__construct(new BookingController());
    }

    protected function handleCustomGet(?string $action, string|int|null $id)
    {
        $enumAction = BookingActions::tryFrom($action);
        return match ($enumAction) {
            BookingActions::GET => $this->controller->get($id),
            default => parent::handleFailedCustomGet($action),
        };
    }

    protected function handleCustomPost(?string $action, ?array $data)
    {
        if (!$action) {
            return response(['status' => false, 'message' => 'No action specified', 'code' => StatusCode::BAD_REQUEST]);
        }

        // Try to map action string to enum
        $actionEnum = BookingActions::tryFrom($action);

        return match ($actionEnum) {
            BookingActions::CREATE => $this->controller->create($data),
            BookingActions::GET => $this->controller->get($data),
            BookingActions::GET_ALL => $this->controller->getMyBookings(),
            BookingActions::UPDATE => $this->controller->update($data),
            BookingActions::DELETE => $this->controller->delete($data),
            default => parent::handleFailedCustomPost($action),
        };
    }

    protected function handleCustomPut(?string $action, ?array $data)
    {
        $enumAction = BookingActions::tryFrom($action);
        return match ($enumAction) {
            BookingActions::UPDATE => $this->controller->update($data),
            default => parent::handleFailedCustomPut($action),
        };
    }

    protected function handleCustomDelete(?string $action, ?array $data)
    {
        $enumAction = BookingActions::tryFrom($action);
        return match ($enumAction) {
            BookingActions::DELETE => $this->controller->delete($data),
            default => parent::handleFailedCustomDelete($action),
        };
    }
}
