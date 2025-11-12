<?php
namespace MyApp\Routes;
use MyApp\Controllers\ProductController;
use MyApp\Routes\BaseRoutes;
use MyApp\Enums\ProductAction;

class ProductRoutes extends BaseRoutes
{
    public function __construct()
    {
        parent::__construct(new ProductController());
    }

    protected function handleCustomGet(?string $action, string|int|null $id)
    {
        $enumAction = ProductAction::tryFrom($action);
        return match ($enumAction) {
            ProductAction::FEATURED => $this->controller->getFeaturedProducts(),
            ProductAction::SEARCH => $this->controller->searchProducts(),
            ProductAction::CATEGORY => $this->controller->getProductsByCategory($id),
            default => parent::handleFailedCustomGet($action),
        };
    }

    protected function handleCustomPost(?string $action, ?array $data)
    {
        $enumAction = ProductAction::tryFrom($action);
        return match ($enumAction) {
            ProductAction::CREATE => $this->controller->createProduct($data),
            default => parent::handleFailedCustomPost($action),
        };
    }

    protected function handleCustomPut(?string $action, ?array $data)
    {
        $enumAction = ProductAction::tryFrom($action);
        return match ($enumAction) {
            ProductAction::UPDATE => $this->controller->updateProduct($data),
            default => parent::handleFailedCustomPut($action),
        };
    }

    protected function handleCustomDelete(?string $action, ?array $data)
    {
        $enumAction = ProductAction::tryFrom($action);
        return match ($enumAction) {
            ProductAction::DELETE => $this->controller->deleteProduct($data),
            default => parent::handleFailedCustomDelete($action),
        };
    }
}
