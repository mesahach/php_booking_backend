<?php
namespace MyApp\Controllers;

use MyApp\Models\CelebrityModel;
use MyApp\Utils\FilePathManagerClass;
use MyApp\Utils\ValidatorHelper as v;
use PH7\JustHttp\StatusCode;

class CelebrityController extends FunctionsController
{
    protected CelebrityModel $model;
    private const DATE_TIME_FORMAT = 'Y-m-d H:i:s';

    public function __construct()
    {
        $this->model = new CelebrityModel();
    }

    public function create(array $data)
    {
        $requiredInputs = [
            'name',
            'status',
            'description'
        ];
        $this->validateParams($data, $requiredInputs);
        $validators = [
            v::validateString($data['name'], 'Fullname', 2, 50),
            v::validateString($data['status'], 'Status', 2, 50),
            v::validateTextArea($data['description'], "Description", 0, false),
        ];

        if (isset($_FILES['image']) && !empty($_FILES['image']["name"])) {
            $ObjFileMg = new FilePathManagerClass();
            $dir = $ObjFileMg->getProfileImageFolder();

            $file = uploadFile($dir, 'image');
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

    public function get(int|string $uuid)
    {
        $validator = v::validateUuid($uuid);
        if (!$validator['status']) {
            return $validator;
        }
        $admin = $this->model->get($uuid);
        if (!$admin) {
            return ['status' => false, 'message' => 'User not found', 'code' => StatusCode::NOT_FOUND];
        }
        return [
            'status' => true,
            'message' => 'User found',
            'data' => $admin->__toArray(),
            'code' => StatusCode::OK
        ];
    }

    public function getAll()
    {
        $admins = $this->model->getAll();
        return [
            'status' => true,
            'message' => 'Users found',
            'data' => $admins,
            'code' => StatusCode::OK
        ];
    }
}