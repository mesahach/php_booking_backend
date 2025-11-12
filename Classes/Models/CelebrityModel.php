<?php
namespace MyApp\Models;

use Ramsey\Uuid\Uuid;
use MyApp\Entity\CelebrityModelEntity;
use MyApp\DAL\CelebrityDal;
use PH7\JustHttp\StatusCode;
use Exception;
use PH7\PhpHttpResponseHeader\Http;
use MyApp\Validation\Exception\InvalidUserException;
use DateTimeImmutable;

class CelebrityModel
{
    public const DATE_TIME_FORMAT = 'Y-m-d H:i:s';
    private readonly string $userUuid;

    public function create(array $data)
    {
        $userUuid = Uuid::uuid4();
        $celebrityModel = new CelebrityModelEntity();
        $celebrityModel->setUuid($userUuid)
        ->setName($data['name'])
        ->setImage($data['image'])
        ->setStatus($data['status'])
        ->setDescription($data['description'] ?? "")
        ->setCreatedAt(date(self::DATE_TIME_FORMAT))
        ->setUpdatedAt(date(self::DATE_TIME_FORMAT));

        try {
            $celebrityDal = new CelebrityDal();
            $celebrityDal->create($celebrityModel);
            return ['status' => true, 'message' => 'User created', 'data' => $celebrityModel->__toArray(), 'code' => StatusCode::CREATED];
        } catch (Exception $e) {
            return ['status' => false, 'message' => $e->getMessage(), 'code' => StatusCode::EXPECTATION_FAILED];
        }
    }

    public function get(int|string $uuid): CelebrityModelEntity|null
    {
        try {
            $celebrity = CelebrityDal::get($uuid);
            if (!$celebrity) {
                return null;
            }
            return $celebrity;
        } catch (Exception $e) {
            return null;
        }
    }

    public function getAll(): array
    {
        $celebrities = CelebrityDal::getAll();
        if (!$celebrities) {
            return [];
        }
        return $celebrities;
    }

    public function update(int|string $uuid, array $data): array
    {
        try {
            $celebrity = new CelebrityModelEntity();
            $celebrity->setUuid($uuid);
            if (isset($data['name'])) {
                $celebrity->setName($data['name']);
            }
            if (isset($data['image'])) {
                $celebrity->setImage($data['image']);
            }
            if (isset($data['status'])) {
                $celebrity->setStatus($data['status']);
            }
            if (isset($data['description'])) {
                $celebrity->setDescription($data['description']);
            }
            $celebrity->setUpdatedAt(date(self::DATE_TIME_FORMAT));

            if (CelebrityDal::update($uuid, $celebrity)) {
                return ['status' => true, 'message' => 'User updated', 'data' => $data, 'code' => StatusCode::OK];
            }
            return ['status' => false, 'message' => 'Count not update user', 'code' => StatusCode::EXPECTATION_FAILED];
        } catch (Exception $e) {
            return ['status' => false, 'message' => $e->getMessage(), 'code' => StatusCode::EXPECTATION_FAILED];
        }
    }
}