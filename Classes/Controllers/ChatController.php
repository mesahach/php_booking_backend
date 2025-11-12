<?php
namespace MyApp\Controllers;

use MyApp\Models\ChatModel;
use MyApp\Utils\FilePathManagerClass;
use MyApp\Utils\ValidatorHelper as v;
use PH7\JustHttp\StatusCode;
use MyApp\Utils\AuthMiddleware;

class ChatController extends FunctionsController
{
    protected ChatModel $model;
    private const DATE_TIME_FORMAT = 'Y-m-d H:i:s';

    public function __construct()
    {
        $this->model = new ChatModel();
    }

    public function create(array $data): array
    {
        $requiredInputs = [
            'message',
        ];
        $this->validateParams($data, $requiredInputs);
        $validators = [
            v::validateString($data['message'], 'Message', 2, 50),
        ];
        $data['sender'] = $data['is_user'] ? 'User' : 'Admin';

        $this->validateInputs($validators);
        return $this->model->create($data);
    }

    public function getMyChats(int|string $uuid): array
    {
        $chats = $this->model->getMyChats($uuid);
        $chats = array_map(fn($chat) => $chat->__toArray(), $chats);
        return ['status' => true, 'message' => 'Chats found', 'data' => $chats, 'code' => StatusCode::OK];
    }

    public function getAll(): array
    {
        $chats = $this->model->getAll();
        $chats = array_map(fn($chat) => $chat->__toArray(), $chats);
        return ['status' => true, 'message' => 'Chats found', 'data' => $chats, 'code' => StatusCode::OK];
    }
}