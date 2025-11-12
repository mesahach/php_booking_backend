<?php
namespace MyApp\Routes;
use MyApp\Controllers\UserController;
use MyApp\Routes\BaseRoutes;
use MyApp\Enums\UserAction;

class UserRoutes extends BaseRoutes
{
    public function __construct()
    {
        parent::__construct(new UserController());
    }

    protected function handleCustomGet(?string $action, string|int|null $id)
    {
        $enumAction = UserAction::tryFrom($action);
        return match ($enumAction) {
            UserAction::GET => $this->controller->get(),
            UserAction::PROFILE => $this->controller->getProfile(),
            // UserAction::SEARCH => $this->controller->searchUsers(),
            UserAction::IS_LOGGED_IN => $this->controller->isLoggedIn(),
            default => parent::handleFailedCustomGet($action),
        };
    }

    protected function handleCustomPost(?string $action, ?array $data)
    {
        $enumAction = UserAction::tryFrom($action);
        return match ($enumAction) {
            UserAction::LOGIN => $this->controller->login($data),
            UserAction::CREATE => $this->controller->createUser($data),
            UserAction::RESEND_EMAIL_OTP => $this->controller->resendEmailOTP($data),
            UserAction::VERIFY_EMAIL => $this->controller->verifyEmailOTP($data),
            UserAction::IS_USER_EXIST => $this->controller->isUserExist($data),
            UserAction::CHANGE_PASSWORD => $this->controller->changePassword($data),
            UserAction::DEACTIVATE => $this->controller->deactivate($data),
            UserAction::UPDATE_PROFILE => $this->controller->update($data),
            UserAction::REFRESH => $this->controller->refreshAccessToken(),
            UserAction::LOGOUT => $this->controller->logout(),
            default => parent::handleFailedCustomPost($action),
        };
    }

    protected function handleCustomPut(?string $action, ?array $data)
    {
        $enumAction = UserAction::tryFrom($action);
        return match ($enumAction) {
            UserAction::CHANGE_PASSWORD => $this->controller->updatePassword($data),
            UserAction::UPDATE => $this->controller->update($data),
            default => parent::handleFailedCustomPut($action),
        };
    }

    protected function handleCustomDelete(?string $action, ?array $data)
    {
        $enumAction = UserAction::tryFrom($action);
        return match ($enumAction) {
            UserAction::DEACTIVATE => $this->controller->deactivate($data),
            UserAction::DELETE => $this->controller->delete($data),
            UserAction::LOGOUT => $this->controller->logout(),
            default => parent::handleFailedCustomDelete($action),
        };
    }
}