<?php 
namespace MyApp\DAL;

use RedBeanPHP\R;
use \RedBeanPHP\RedException\SQL as SQLException;
use RedBeanPHP\SimpleModel;
use MyApp\Entity\FoodItemModelEntity;

class FoodItemDal extends SimpleModel
{
    private const TABLE_NAME = 'fooditems';

    public function create(FoodItemModelEntity $entity): bool|int|string
    {
        $foodItem = R::dispense(self::TABLE_NAME);
        $foodItem->uuid = $entity->getUuid();
        $foodItem->name = $entity->getName();
        $foodItem->price = $entity->getPrice();
        $foodItem->description = $entity->getDescription();
        $foodItem->image = $entity->getImage();
        $foodItem->created_at = $entity->getCreatedAt();
        $foodItem->updated_at = $entity->getUpdatedAt();
        try {
            $id = R::store($foodItem);
            return $id;
        } catch (\Exception $e) {
            return false;
        } finally {
            R::close();
        }
    }

    public static function get(string $id): FoodItemModelEntity|null
    {
        try {
            $foodItem = R::findOne(self::TABLE_NAME, "uuid = :uuid", [':uuid' => $id]);
            return $foodItem ? (new FoodItemModelEntity())->__unserialize($foodItem->export()) : null;
        } catch (\Exception $e) {
            return null;
        } finally {
            R::close();
        }
    }

    public static function getAll(): array
    {
        try {
            $foodItems = R::findAll(self::TABLE_NAME);
            return $foodItems;
        } catch (\Exception $e) {
            return [];
        } finally {
            R::close();
        }
    }

    public static function update(string $uid, FoodItemModelEntity $entity)
    {
        $foodItemBean = R::findOne(self::TABLE_NAME, 'uuid = :uuid', [':uuid' => $uid]);
        $foodItemBean->uuid = $entity->getUuid();

        // Always keep UUID in sync
        $foodItemBean->uuid = $entity->getUuid();

        // Update fields only if not null
        if ($entity->getName() !== null) {
            $foodItemBean->name = $entity->getName();
        }

        if ($entity->getPrice() !== null) {
            $foodItemBean->price = $entity->getPrice();
        }

        if ($entity->getDescription() !== null) {
            $foodItemBean->description = $entity->getDescription();
        }

        if ($entity->getImage() !== null) {
            $foodItemBean->image = $entity->getImage();
        }

        // Always update timestamp
        $foodItemBean->updated_at = $entity->getUpdatedAt();
        try {
            $id = R::store($foodItemBean);
            R::close();
            return true;
        } catch (SQLException $e) {
            return false;
        } finally {
            R::close();
        }
    }

    public static function delete(string $uid)
    {
        try {
            $foodItemBean = R::findOne(self::TABLE_NAME, 'uuid = :uuid', [':uuid' => $uid]);
            if (!$foodItemBean) {
                return false;
            }
            $id = (bool) R::trash($foodItemBean);
            return $id;
        } catch (SQLException $e) {
            return false;
        } finally {
            R::close();
        }
    }
}