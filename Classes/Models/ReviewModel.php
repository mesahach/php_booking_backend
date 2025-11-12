<?php
namespace MyApp\Models;

use Ramsey\Uuid\Uuid;
use MyApp\Entity\ReviewModelEntity;
use MyApp\DAL\ReviewDal;
use PH7\JustHttp\StatusCode;
use Exception;

class ReviewModel
{
    public const DATE_TIME_FORMAT = 'Y-m-d H:i:s';
    private readonly string $userUuid;

    public function create(array $data)
    {
        $uuid = Uuid::uuid4();
        $reviewModel = new ReviewModelEntity();
        $reviewModel->setUuid($uuid)
        ->setCelebrityUuid($data['celebrityUuid'])
        ->setUserUuid($data['userUuid'])
        ->setName($data['name'])
        ->setImage($data['image'])
        ->setReview($data['review'])
        ->setStars($data['stars'])
        ->setCreatedAt(date(self::DATE_TIME_FORMAT))
        ->setUpdatedAt(date(self::DATE_TIME_FORMAT));

        try {
            $reviewDal = new ReviewDal();
            $reviewDal->create($reviewModel);
            return ['status' => true, 'message' => 'Review created', 'data' => $reviewModel->__toArray(), 'code' => StatusCode::CREATED];
        } catch (Exception $e) {
            return ['status' => false, 'message' => $e->getMessage(), 'code' => StatusCode::EXPECTATION_FAILED];
        }
    }

    public function get(int|string $uuid): ReviewModelEntity|null
    {
        try {
            $review = ReviewDal::get($uuid);
            if (!$review) {
                return null;
            }

            return $review;
        } catch (Exception $e) {
            return null;
        }
    }

    public function getAll(): array
    {
        return ReviewDal::getAll();
    }

    public function getMyReviews(int|string $uuid): array
    {
        $reviews = ReviewDal::getMyReviews($uuid);
        $reviews = array_map(function($r): array {
            $reviewObj = new ReviewModelEntity();
            $reviewObj->setUuid($r->getUuid());
            $reviewObj->setCelebrityUuid($r->getCelebrityUuid());
            $reviewObj->setUserUuid($r->getUserUuid());
            $reviewObj->setName($r->getName());
            $reviewObj->setImage($r->getImage());
            $reviewObj->setReview($r->getReview());
            $reviewObj->setStars($r->getStars());
            $reviewObj->setCreatedAt($r->getCreatedAt());
            $reviewObj->setUpdatedAt($r->getUpdatedAt());
           $review = $reviewObj->__toArray();
            return $review;
        }, $reviews);
        return $reviews;
    }

    public function delete(int|string $uuid): bool
    {
        try {
            $review = ReviewDal::get($uuid);
            if (!$review) {
                return false;
            }
            return ReviewDal::delete($uuid);
        } catch (Exception $e) {
            return false;
        }
    }
}