<?php 
namespace MyApp\Models;

use Exception;
use PH7\JustHttp\StatusCode;
use MyApp\DAL\FoodItemDal;
use MyApp\Entity\FoodItemModelEntity;
use Ramsey\Uuid\Uuid;

class FoodItemModel
{
    public const DATE_TIME_FORMAT = 'Y-m-d H:i:s';
    private readonly string $uuid;
    private FoodItemModelEntity $entity;

    public function __construct()
    {
        $this->entity = new FoodItemModelEntity();
    }

    public function setUuid(string $uuid): self
    {
        $this->uuid = $uuid;
        return $this;
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }
    
    public function create(array $data)
    {
        $this->entity->setUuid(Uuid::uuid4()->toString());
        $this->entity->setName($data['name']);
        $this->entity->setPrice($data['price']);
        $this->entity->setDescription($data['description']);
        $this->entity->setImage($data['image']);
        $this->entity->setCreatedAt(date(self::DATE_TIME_FORMAT));
        $this->entity->setUpdatedAt(date(self::DATE_TIME_FORMAT));
        try {        
            $foodItemDal = new FoodItemDal();    
            $foodItemDal->create($this->entity);
            return ['status' => true, 'message' => 'Food item created', 'food_item' => $data, 'code' => StatusCode::CREATED];
        } catch (Exception $e) {
            return ['status' => false, 'message' => $e->getMessage(), 'code' => StatusCode::EXPECTATION_FAILED];
        }
    }

    public function get($id): array
    {
        try {
            $foodItem = FoodItemDal::get($id);
            $code = $foodItem ? StatusCode::OK : StatusCode::NOT_FOUND;
            $message = $foodItem ? 'Food item found' : 'Food item not found';
            $status = $foodItem ? true : false;
            unset($foodItem['uuid'], $foodItem['id']);
            return ['status' => $status, 'message' => $message, 'data' => $foodItem, 'code' => $code];
        } catch (Exception $e) {
            return ['status' => false, 'message' => $e->getMessage(), 'code' => StatusCode::EXPECTATION_FAILED];
        }
    }

    public function getAll(): array
    {
        try {
            $foodItems = FoodItemDal::getAll();
            $foodItems = array_map(fn($foodItem) => [
            // 'uuid' => $foodItem['data']->uuid,
            'name' => $foodItem->name,
            'price' => $foodItem->price,
            'description' => $foodItem->description,
            'image' => $foodItem->image,
        ], $foodItems);
            return ['status' => true, 'message' => 'Food items found', 'data' => $foodItems, 'code' => StatusCode::OK];
        } catch (Exception $e) {
            return ['status' => false, 'message' => $e->getMessage(), 'code' => StatusCode::EXPECTATION_FAILED];
        }
    }

    public function update(int|string $id, array $data): array
    {
        try {
            $foodItem = new FoodItemModelEntity();
            $foodItem->setUuid($id);
            if (isset($data['name'])) {
                $foodItem->setName($data['name']);
            }
            if (isset($data['price'])) {
                $foodItem->setPrice($data['price']);
            }
            if (isset($data['description'])) {
                $foodItem->setDescription($data['description']);
            }
            if (isset($data['image'])) {
                $foodItem->setImage($data['image']);
            }
            $foodItem->setUpdatedAt(date(self::DATE_TIME_FORMAT));

            if (FoodItemDal::update($id, $foodItem)) {
                unset($data['id']);
                return [
                    'status' => true, 'message' => "Food item updated", 'data' => $data, 'code' => StatusCode::OK,
                ];
            }else {
                return ['status' => false, 'message' => "Failed to update", 'code' => StatusCode::EXPECTATION_FAILED,];
            }
        } catch (\Throwable $e) {
            return ['status' => false, 'message' => $e->getMessage(), 'code' => StatusCode::EXPECTATION_FAILED];
        }
    }

    public function delete(string|int $id): array
    {
        try {
            $foodItem = FoodItemDal::delete($id);
            if ($foodItem) {
                return [
                    'status' => true,
                    'message' => "Food item deleted",
                    'code' => StatusCode::OK
                ];
            }
            return [
                'status' => false,
                'message' => "Failed to delete item",
                'code' => StatusCode::EXPECTATION_FAILED
            ];
        } catch (\Throwable $e) {
            return ['status' => false, 'message' => $e->getMessage(), 'code' => StatusCode::EXPECTATION_FAILED];
        }
    }
}