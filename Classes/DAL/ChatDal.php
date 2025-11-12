<?php
namespace MyApp\DAL;

use RedBeanPHP\R;
use RedBeanPHP\RedException\SQL;
use MyApp\Entity\ChatModelEntity;

class ChatDal
{
    public const TABLE_NAME = 'chats';

    public function create(ChatModelEntity $data): int|string|false
    {
        $bean = R::dispense(self::TABLE_NAME);
        $bean->uuid = $data->getUuid();
        $bean->sender_uuid = $data->getSenderUuid();
        $bean->message = $data->getMessage();
        $bean->sender = $data->getSender();
        $bean->created_at = $data->getCreatedAt();
        $bean->updated_at = $data->getUpdatedAt();

        try {
            $id = R::store($bean);
            R::exec('ALTER TABLE ' . self::TABLE_NAME . ' ADD UNIQUE (uuid)');
        } catch (SQL $e) {
            return false;
        } finally {
            R::close();
        }

        return $id;
    }

    public static function get(int|string $id): ?ChatModelEntity
    {
        try {
            $bean = is_numeric($id)
                ? R::load(self::TABLE_NAME, $id)
                : R::findOne(self::TABLE_NAME, 'uuid = ?', [$id]);
            if (!$bean || !$bean->id)
                return null;
            return (new ChatModelEntity())->__fromArray($bean->export());
        } finally {
            R::close();
        }
    }

    public static function getMyChats(int|string $uuid): array
    {
        try {
            $beans = R::findAll(self::TABLE_NAME, "sender_uuid = ?", [$uuid]);
            return array_map(fn($b) => (new ChatModelEntity())->__fromArray($b->export()), $beans);
        } finally {
            R::close();
        }
    }

    public static function findByUuid(string $uuid): ?ChatModelEntity
    {
        try {
            $bean = R::findOne(self::TABLE_NAME, 'uuid = ?', [$uuid]);
            if (!$bean)
                return null;
            return (new ChatModelEntity())->__fromArray($bean->export());
        } finally {
            R::close();
        }
    }

    public static function getAll(): array
    {
        try {
            $beans = R::findAll(self::TABLE_NAME, "ORDER BY created_at DESC");
            return array_map(fn($b) => (new ChatModelEntity())->__fromArray($b->export()), $beans);
        } finally {
            R::close();
        }
    }

    public static function delete(string $uuid): bool
    {
        try {
            $bean = R::findOne(self::TABLE_NAME, 'uuid = ?', [$uuid]);
            if (!$bean)
                return false;
            R::trash($bean);
            return true;
        } finally {
            R::close();
        }
    }
}
