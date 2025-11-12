<?php
namespace MyApp\Controllers;

use MyApp\Models\ReviewModel;
use MyApp\Utils\FilePathManagerClass;
use MyApp\Utils\ValidatorHelper as v;
use PH7\JustHttp\StatusCode;

class ReviewController extends FunctionsController
{
    protected ReviewModel $model;
    private const DATE_TIME_FORMAT = 'Y-m-d H:i:s';

    public function __construct()
    {
        $this->model = new ReviewModel();
    }

    public function createAdmin(array $data): array
    {
        $requiredInputs = [
            'celebrityUuid',
            'userUuid',
            'name',
            'review',
            'stars',
        ];
        $this->validateParams($data, $requiredInputs);
        $validators = [
            // v::validateString($data['celebrityUuid'], 'Celebrity UUID', 2, 50),
            // v::validateString($data['userUuid'], 'User UUID', 2, 50),
            v::validateString($data['name'], 'Name', 2, 50),
            v::validateString($data['review'], 'Review', 2, 50),
            v::validateNumber($data['stars']),
        ];
        // $data['sender'] = $data['is_user'] ? 'User' : 'Admin';
        $data['image'] = null;

        if (isset($_FILES['image']) && !empty($_FILES['image']["name"])) {
            $ObjFileMg = new FilePathManagerClass();
            $dir = $ObjFileMg->getProfileImageFolder();

            $file = uploadFile(targetDir: $dir, fieldName: 'image');
            if ($file['status']) {
                $data['image'] = $file['filename'];
            } else {
                return [
                    'code' => 412,
                    'message' => $file['message'],
                    'status' => false,
                ];
            }
        }

        $this->validateInputs($validators);
        return $this->model->create($data);
    }

    public function get(int|string $uuid): array
    {
        $review = $this->model->get($uuid);
        if (!$review) {
            return ['status' => false, 'message' => 'Review not found', 'code' => StatusCode::NOT_FOUND];
        }
        return ['status' => true, 'data' => $review->__toArray(), 'code' => StatusCode::OK];
    }

    public function getAll(): array
    {
        $reviews = $this->model->getAll();
        if (!$reviews) {
            return ['status' => false, 'message' => 'Reviews not found', 'code' => StatusCode::NOT_FOUND];
        }
        return ['status' => true, 'data' => $reviews, 'code' => StatusCode::OK];
    }

    public function getMyReviews(int|string $uuid): array
    {
        $reviews = $this->model->getMyReviews($uuid);
        if (!$reviews) {
            return ['status' => false, 'message' => 'Reviews not found', 'code' => StatusCode::NOT_FOUND];
        }
        return ['status' => true, 'data' => $reviews, 'code' => StatusCode::OK];
    }

    public function deleteAdmin(array $data): array
    {
        $requiredInputs = [
            'uuid',
        ];
        $this->validateParams($data, $requiredInputs);
        $validator = v::validateUuid($data['uuid']);
        if (!$validator['status']) {
            return $validator;
        }
        $review = $this->model->delete($data['uuid']);
        if (!$review) {
            return ['status' => false, 'message' => 'Failed to delete review', 'code' => StatusCode::EXPECTATION_FAILED];
        }
        return ['status' => true, 'message' => 'Review deleted', 'code' => StatusCode::OK];
    }
}