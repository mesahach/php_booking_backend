<?php 
declare(strict_types=1);
namespace MyApp\Routes;

use MyApp\Controllers\FoodItemController;
use MyApp\Routes\BaseRoutes;
use MyApp\Enums\FoodItemAction;

class FoodItemRoutes extends BaseRoutes
{
    public function __construct()
    {
        parent::__construct(new FoodItemController());
    }

    protected function handleCustomGet(?string $action, string|int|null $id)
    {
        $enumAction = FoodItemAction::tryFrom($action);
        return match ($enumAction) {
            FoodItemAction::GET => $this->controller->get($id),
            FoodItemAction::GET_ALL => $this->controller->getAll(),
            default => parent::handleFailedCustomGet($action),
        };
    }

    protected function handleCustomPost(?string $action, ?array $data)
    {
        $enumAction = FoodItemAction::tryFrom($action);
        return match ($enumAction) {
            FoodItemAction::CREATE => $this->controller->createFoodItem($data),
            default => parent::handleFailedCustomPost($action),
        };
    }

    protected function handleCustomPut(?string $action, ?array $data)
    {
        $enumAction = FoodItemAction::tryFrom($action);
        return match ($enumAction) {
            FoodItemAction::UPDATE => $this->controller->updateFoodItem($data),
            default => parent::handleFailedCustomPut($action),
        };
    }

    protected function handleCustomDelete(?string $action, ?array $data)
    {
        $enumAction = FoodItemAction::tryFrom($action);
        return match ($enumAction) {
            FoodItemAction::DELETE => $this->controller->deleteFoodItem($data),
            default => parent::handleFailedCustomDelete($action),
        };
    }
}