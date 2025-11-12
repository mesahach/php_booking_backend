<?php
namespace MyApp\Models;

use Ramsey\Uuid\Uuid;
use MyApp\Entity\ChatModelEntity;
use MyApp\DAL\ChatDal;
use PH7\JustHttp\StatusCode;
use Exception;

class ChatModel
{
    public const DATE_TIME_FORMAT = 'Y-m-d H:i:s';

    public function create(array $data)
    {
        $userUuid = Uuid::uuid4();
        $chatModel = new ChatModelEntity();
        $chatModel->setUuid($userUuid)
            ->setSenderUuid($data['sender_uuid'])
            ->setMessage($data['message'])
            ->setSender($data['sender'])
            ->setCreatedAt(date(self::DATE_TIME_FORMAT))
            ->setUpdatedAt(date(self::DATE_TIME_FORMAT));

        try {
            $chatDal = new ChatDal();
            $chatDal->create($chatModel);
            return ['status' => true, 'message' => 'Chat created', 'data' => $chatModel->__toArray(), 'code' => StatusCode::CREATED];
        } catch (Exception $e) {
            return ['status' => false, 'message' => $e->getMessage(), 'code' => StatusCode::EXPECTATION_FAILED];
        }
    }

    public function get(int|string $uuid): ChatModelEntity|null
    {
        try {
            $chat = ChatDal::get($uuid);
            if (!$chat) {
                return null;
            }

            return $chat;
        } catch (Exception $e) {
            return null;
        }
    }

    public function getAll(): array
    {
        $chats = ChatDal::getAll();
        return $chats;
    }

    public function getMyChats(int|string $uuid): array
    {
        $chats = ChatDal::getMyChats($uuid);
        return $chats;
    }
}