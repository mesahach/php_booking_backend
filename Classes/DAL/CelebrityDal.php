<?php
declare(strict_types=1);

namespace MyApp\DAL;

use RedBeanPHP\R;
use RedBeanPHP\SimpleModel;
use MyApp\Entity\CelebrityModelEntity;
use \RedBeanPHP\RedException\SQL as SQLException;

final class CelebrityDal extends SimpleModel
{
    private const TABLE_NAME = 'celebrities';


    public function create(CelebrityModelEntity $data): int|string|false
    {
        $celebrity = R::dispense(self::TABLE_NAME);
        $celebrity->uuid = $data->getUuid();
        $celebrity->name = $data->getName();
        $celebrity->image = $data->getImage();
        $celebrity->description = $data->getDescription();
        $celebrity->status = $data->getStatus();
        $celebrity->created_at = $data->getCreatedAt();
        $celebrity->updated_at = $data->getUpdatedAt();
        try {
            $id = R::store($celebrity);
            // Ensure unique index on uuid
            R::exec('ALTER TABLE ' . self::TABLE_NAME . ' ADD UNIQUE (uuid)');
        } catch (SQLException $e) {
            return false;
        } finally {
            R::close();
        }
        return $id;
    }

    public static function get(int|string $uuid): ?CelebrityModelEntity
    {
        try {
            $celebrity = is_numeric($uuid) ? R::load(self::TABLE_NAME, $uuid) : R::findOne(self::TABLE_NAME, 'uuid = :uuid', ['uuid' => $uuid]);
            
            return $celebrity ? (new CelebrityModelEntity())->__fromArray($celebrity->export()) : null;
        } catch (SQLException $e) {
            return null;
        } finally {
            R::close();
        }
    }

    public static function getAll(): array
    {
        $celebrities = R::findAll(self::TABLE_NAME);
        $celebrities = array_map(function($celebrity) {
            unset($celebrity->id);
            return $celebrity;
        }, $celebrities);
        return $celebrities;
    }
    
    public static function update(int|string $uuid, CelebrityModelEntity $data): bool
    {
        $celebrity = R::findOne(self::TABLE_NAME, 'uuid = :uuid', ['uuid' => $uuid]);

        $name = $data->getName();
        if ($name) {
            $celebrity->name = $name;
        }
        $status = $data->getStatus();
        if ($status) {
            $celebrity->status = $status;
        }
        $image = $data->getImage();
        if ($image) {
            $celebrity->image = $image;
        }
        $description = $data->getDescription();
        if ($description) {
            $celebrity->description = $description;
        }
        try {
            $id = R::store($celebrity);
            R::close();
            return true;
        } catch (SQLException $e) {
            return false;
        } finally {
            R::close();
        }
    }
}