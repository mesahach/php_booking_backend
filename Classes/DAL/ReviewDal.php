<?php
declare(strict_types=1);

namespace MyApp\DAL;

use RedBeanPHP\R;
use RedBeanPHP\SimpleModel;
use MyApp\Entity\ReviewModelEntity;
use \RedBeanPHP\RedException\SQL as SQLException;

final class ReviewDal extends SimpleModel
{
    private const TABLE_NAME = 'reviews';

    public function create(ReviewModelEntity $data): int|string|false
    {
        $review = R::dispense(self::TABLE_NAME);
        $review->uuid = $data->getUuid();
        $review->celebrityUuid = $data->getCelebrityUuid();
        $review->userUuid = $data->getUserUuid();
        $review->name = $data->getName();
        $review->image = $data->getImage();
        $review->review = $data->getReview();
        $review->stars = $data->getStars();
        $review->created_at = $data->getCreatedAt();
        $review->updated_at = $data->getUpdatedAt();
        try {
            $id = R::store($review);
            // Ensure unique index on uuid
            R::exec('ALTER TABLE ' . self::TABLE_NAME . ' ADD UNIQUE (uuid)');
        } catch (SQLException $e) {
            return false;
        } finally {
            R::close();
        }
        return $id;
    }

    public static function get(int|string $uuid): ?ReviewModelEntity
    {
        try {
            $review = is_numeric($uuid) ? R::load(self::TABLE_NAME, $uuid) : R::findOne(self::TABLE_NAME, 'uuid = :uuid', ['uuid' => $uuid]);

            return $review ? (new ReviewModelEntity())->__fromArray($review->export()) : null;
        } catch (SQLException $e) {
            return null;
        } finally {
            R::close();
        }
    }

    public static function getMyReviews(int|string $uuid): array
    {
        try {
            $beans = R::findAll(self::TABLE_NAME, "user_uuid = ?", [$uuid]);
            return array_map(function ($b): ReviewModelEntity {
                unset($b->user_uuid);
                return (new ReviewModelEntity())->__fromArray($b->export());
            }, $beans);
        } finally {
            R::close();
        }
    }

    public static function getAll(): array
    {
        $reviews = R::findAll(self::TABLE_NAME);
        $reviews = array_map(function ($r) {
            unset($r->user_uuid, $r->id);
            return $r;
        }, $reviews);
        return $reviews;
    }

    public static function delete(int|string $uuid): bool
    {
        try {
            $review = R::findOne(self::TABLE_NAME, "uuid = :uuid", ["uuid" => $uuid]);
            if (!$review) {
                return false;
            }
            R::trash($review);
            return true;
        } catch (SQLException $e) {
            return false;
        } finally {
            R::close();
        }
    }
}