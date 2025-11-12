<?php 
namespace MyApp\Controllers;

use MyApp\Models\FoodItemModel;
use MyApp\Utils\ValidatorHelper as v;
use PH7\JustHttp\StatusCode;

class FoodItemController extends FunctionsController
{
    protected FoodItemModel $model;
    private const DATE_TIME_FORMAT = 'Y-m-d H:i:s';

    public function __construct()
    {
        $this->model = new FoodItemModel();
    }

    public function create(array $data)
    {
        $requiredInputs = [
            'name',
            'price',
            'description',
            'image',
        ];
        $this->validateParams($data, $requiredInputs);
        $validators = [
            v::validateString($data['name'], 'Name', 2, 50),
            v::validateFloat($data['price'], 'Price'),
            v::validateString($data['description'], 'Description', 2, 50),
            v::validateString($data['image'], 'Image', 2, 255, false),
        ];
        $this->validateInputs($validators);
       return $this->model->create($data);
    }

    public function get(int|string $uuid)
    {
        $validator = v::validateUuid($uuid);
        if (!$validator['status']) {
            return $validator;
        }
        return $this->model->get($uuid);
    }

    public function getAll()
    {
        $allFoodItems = $this->model->getAll();
        if (!$allFoodItems) {
            return ['status' => false, 'message' => "Failed to fetch food items", 'code' => StatusCode::EXPECTATION_FAILED];
        }
        return $allFoodItems;
    }

    public function update(array $data)
    {
        $requiredInputs = [
            'id',
        ];
        $this->validateParams($data, $requiredInputs);

        $validator = v::validateUuid($data['id']);
        if (!$validator['status']) {
            return $validator;
        }

        if (isset($_FILES['image'])) {
            $ObjFileMg = new \MyApp\Utils\FilePathManagerClass();
            $dir = $ObjFileMg->getDataImageFolder();
            $file = uploadFile($dir, 'image');

            if (empty($file['filename'])) {
                // Handle upload error
                return [
                    'code' => StatusCode::UNPROCESSABLE_ENTITY,
                    'message' => $file['message'],
                    'success' => false,
                ];
            }
            $data['image'] = $file['filename'];
        }

        return $this->model->update($data['id'], $data);
    }

    public function delete(array $data) 
    {
        $validator = v::validateUserUuid($data['id']);
        if (!$validator['status']) {
            return $validator;
        }

        return $this->model->delete($data['id']);
    }
}