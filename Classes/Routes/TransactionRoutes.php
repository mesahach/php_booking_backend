<?php
namespace MyApp\Routes;
use MyApp\Controllers\TransactionController;
use MyApp\Routes\BaseRoutes;
use MyApp\Enums\TransactionAction;
use PH7\JustHttp\StatusCode;

class TransactionRoutes extends BaseRoutes
{
    public function __construct()
    {
        parent::__construct(new TransactionController());
    }

    protected function handleCustomGet(?string $action, string|int|null $id)
    {
        $enumAction = TransactionAction::tryFrom($action);
        return match ($enumAction) {
            TransactionAction::VERIFY => $this->controller->verifyTransaction($id),
            TransactionAction::RECENT => $this->controller->getRecentTransactions(),
            TransactionAction::SEARCH => $this->controller->searchTransaction(),
            default  => parent::handleFailedCustomGet($action),
        };
    }

    protected function handleCustomPost(?string $action, ?array $data)
    {
        if (!$action) {
            return response(['status' => false, 'message' => 'No action specified', 'code' => StatusCode::BAD_REQUEST]);
        }

        // Try to map action string to enum
        $actionEnum = TransactionAction::tryFrom($action);

        if (!$actionEnum) {
            return parent::handleFailedCustomPost($action); // fallback to base
        }

        return match ($actionEnum) {
            TransactionAction::CREATE => $this->controller->createTransaction($data),
            TransactionAction::VERIFY => $this->controller->verifyTransaction($data),
            TransactionAction::SEARCH => $this->controller->searchTransactions($data),
            TransactionAction::GET5DATA => $this->controller->getLastFiveTransactions($data),
        };
    }

    protected function handleCustomPut(?string $action, ?array $data)
    {
        $enumAction = TransactionAction::tryFrom($action);
        return match ($enumAction) {
            TransactionAction::UPDATE => $this->controller->updateTransaction($data),
            default => parent::handleFailedCustomPut($action),
        };
    }

    protected function handleCustomDelete(?string $action, ?array $data)
    {
        $enumAction = TransactionAction::tryFrom($action);
        return match ($enumAction) {
            TransactionAction::DELETE => $this->controller->deleteTransaction($data),
            default => parent::handleFailedCustomDelete($action),
        };
    }
}
