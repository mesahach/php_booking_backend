<?php
namespace MyApp\Controllers;

use MyApp\Models\BookingModel;
use MyApp\Models\UserModel;
use MyApp\Models\CelebrityModel;
use MyApp\Utils\ValidatorHelper as v;
use MyApp\Utils\AuthMiddleware;
use PH7\JustHttp\StatusCode;
use MyApp\Utils\NotificationManagerClass;
use MyApp\Utils\EmailMessagesClass;

class BookingController extends FunctionsController
{
    protected BookingModel $model;
    private const DATE_TIME_FORMAT = 'Y-m-d H:i:s';

    public function __construct()
    {
        $this->model = new BookingModel();
    }

    public function create(array $data): array
    {
        $auth = AuthMiddleware::verifyAccessToken();
        if (!$auth['status']) {
            return $auth;
        }
        $validator = v::validateUserUuid($auth['user_uuid']);
        if (!$validator['status']) {
            return $validator;
        }

        $requiredInputs = [
            'celebrityId',
            'booking_date',
            'booking_time',
        ];
        $this->validateParams($data, $requiredInputs);

        $validators = [
            v::validateUuid($data['celebrityId']),
            v::validateDate($data['booking_date'], 'Booking Date'),
            v::validateTime24Hours($data['booking_time'], 'Booking Time'),
        ];
        $this->validateInputs($validators);
        $data['user_uuid'] = $auth['user_uuid'];

        $result = $this->model->create($data);
        if (!$result['status']) {
            return $result;
        }

        $userModel = new UserModel();
        $user = $userModel->get($auth['user_uuid']);
        $notification = new NotificationManagerClass();
        $title = "Booking Created";
        $messageInput = "Congratulations! Your booking has been created, we will get back to you as soon as possible.";
        $message = EmailMessagesClass::setNotificationMessage($title, $messageInput);
        $notification->sendEmail(
            title: $title,
            message: $message,
            userData: $user
        );

        return ['status' => true, 'message' => 'Booking created', 'data' => $result['data'], 'code' => StatusCode::CREATED];
    }

    public function getAllAdmin(): array
    {
        $bookings = $this->model->getAll();

        $userModel = new UserModel();
        $celebrityModel = new CelebrityModel(); 

        foreach ($bookings as $key => $value) {
        $user = $userModel->get($value['user_uuid']);

        $bookings = $this->model->getMyBookings($value['user_uuid']);
            // Initialize arrays
            $bookings[$key]['celebrity'] = ['name' => '', 'image' => ''];
            $bookings[$key]['customer'] = ['name' => ''];

            // Safe: Only get celebrity if UUID exists
            if (!empty($value['celebrityUuid'])) {
                $celebrity = $celebrityModel->get($value['celebrityUuid']);
                if ($celebrity) {
                    $bookings[$key]['celebrity']['name'] = $celebrity->getName() ?? '';
                    $bookings[$key]['celebrity']['image'] = $celebrity->getImage() ?? '';
                }
                // else: leave as empty (celebrity deleted?)
            }

            // Safe: User should exist
            $bookings[$key]['customer']['name'] =
                $user->getFirstName() . ' ' . $user->getLastName();
        }
        return ['status' => true, 'message' => "Bookings fetched", 'data' => $bookings, 'code' => StatusCode::OK];
    }

    public function getAll(): array
    {
        $auth = AuthMiddleware::verifyAccessToken();
        if (!$auth['status']) {
            return $auth;
        }

        $validator = v::validateUserUuid($auth['user_uuid']);
        if (!$validator['status']) {
            return $validator;
        }

        $userModel = new UserModel();
        $user = $userModel->get($auth['user_uuid']);

        $bookings = $this->model->getMyBookings($auth['user_uuid']);

        $celebrityModel = new CelebrityModel(); // Move outside loop (performance)

        foreach ($bookings as $key => $value) {
            // Initialize arrays
            $bookings[$key]['celebrity'] = ['name' => '', 'image' => ''];
            $bookings[$key]['customer'] = ['name' => ''];

            // Safe: Only get celebrity if UUID exists
            if (!empty($value['celebrityUuid'])) {
                $celebrity = $celebrityModel->get($value['celebrityUuid']);
                if ($celebrity) {
                    $bookings[$key]['celebrity']['name'] = $celebrity->getName() ?? '';
                    $bookings[$key]['celebrity']['image'] = $celebrity->getImage() ?? '';
                }
                // else: leave as empty (celebrity deleted?)
            }

            // Safe: User should exist
            $bookings[$key]['customer']['name'] =
                $user->getFirstName() . ' ' . $user->getLastName();
        }

        return [
            'status' => true,
            'message' => "Found your bookings",
            'data' => $bookings,
            'code' => StatusCode::OK,
        ];
    }

    public function update(array $data): array
    {
        $requiredInputs = [
            'booking_date',
            'booking_time',
            'uuid',
        ];
        $this->validateParams($data, $requiredInputs);

        $validators = [
            v::validateDate($data['booking_date'], 'Booking Date'),
            v::validateTime24Hours($data['booking_time'], 'Booking Time'),
            v::validateUuid($data['uuid']),
        ];
        $this->validateInputs($validators);

        $result = $this->model->update($data['uuid'], $data);
        if (!$result) {
                $notification = new NotificationManagerClass();
                $title = "Booking Accepted";
                $messageInput = "Notice: Your booking has been updated";
                $userModel = new UserModel();
                $user = $userModel->get($data['user_uuid']);
                $message = EmailMessagesClass::setNotificationMessage($title, $messageInput);
                $notification->sendEmail(
                    title: $title,
                    message: $message,
                    userData: $user
                );
            
            return ['status' => false, 'message' => 'Failed to update booking', 'code' => StatusCode::EXPECTATION_FAILED];
        }
        return ['status' => true, 'message' => 'Booking updated', 'code' => StatusCode::OK];
    }

    public function editAdmin(array $data): array
    {
        $requiredInputs = [
            'booking_date',
            'booking_time',
            'booking_status',
            'uuid',
        ];
        $this->validateParams($data, $requiredInputs);

        $validators = [
            v::validateDate($data['booking_date'], 'Booking Date'),
            v::validateTime24Hours($data['booking_time'], 'Booking Time'),
            v::validateString($data['booking_status'], 'Booking Status', 2, 50),
            v::validateUuid($data['uuid']),
        ];
        $this->validateInputs($validators);

        $result = $this->model->update($data['uuid'], $data);
        if (!$result) {
            if ($data['booking_status'] == "Accepted") {
                $notification = new NotificationManagerClass();
                $title = "Booking Accepted";
                $messageInput = "Congratulations! Your booking has been accepted";
                $userModel = new UserModel();
                $user = $userModel->get($data['user_uuid']);
                $message = EmailMessagesClass::setNotificationMessage($title, $messageInput);
                $notification->sendEmail(
                    title: $title,
                    message: $message,
                    userData: $user
                );
            }
            return ['status' => false, 'message' => 'Failed to update booking', 'code' => StatusCode::EXPECTATION_FAILED];
        }
        return ['status' => true, 'message' => 'Booking updated', 'code' => StatusCode::OK];
    }
}