<?php
namespace MyApp\Models;

use Ramsey\Uuid\Uuid;
use MyApp\Entity\BookingModelEntity;
use MyApp\DAL\BookingDal;
use PH7\JustHttp\StatusCode;
use Exception;
use PH7\PhpHttpResponseHeader\Http;
use MyApp\Validation\Exception\InvalidUserException;
use DateTimeImmutable;

class BookingModel
{
    public const DATE_TIME_FORMAT = 'Y-m-d H:i:s';
    private readonly string $userUuid;

    public function create(array $data)
    {
        $uuid = Uuid::uuid4();
        $bookingModel = new BookingModelEntity();
        $bookingModel->setUuid($uuid)
            ->setCelebrityUuid($data['celebrityId'])
            ->setUserUuid($data['user_uuid'])
            ->setBookingDate($data['booking_date'])
            ->setBookingTime($data['booking_time'])
            ->setBookingStatus("Pending")
            ->setCreatedAt(date(self::DATE_TIME_FORMAT))
            ->setUpdatedAt(date(self::DATE_TIME_FORMAT));

        try {
            BookingDal::create($bookingModel);
            return ['status' => true, 'message' => 'Booking created', 'data' => $bookingModel->__toArray(), 'code' => StatusCode::CREATED];
        } catch (Exception $e) {
            return ['status' => false, 'message' => $e->getMessage(), 'code' => StatusCode::EXPECTATION_FAILED];
        }
    }

    public function get(int|string $uuid): BookingModelEntity|null
    {
        try {
            $booking = BookingDal::get($uuid);
            if (!$booking) {
                return null;
            }

            return $booking;
        } catch (Exception $e) {
            return null;
        }
    }

    public function getAll(): array
    {
        return BookingDal::getAll();
    }

    public function getMyBookings(int|string $uuid): array
    {
        $bookings = BookingDal::getMyBookings($uuid);
        $bookings = array_map(function($b): array {
            $bookingObj = new BookingModelEntity();
            $bookingObj->setUuid($b->getUuid());
            $bookingObj->setCelebrityUuid($b->getCelebrityUuid());
            $bookingObj->setBookingDate($b->getBookingDate()->format('Y-m-d'));
            $bookingObj->setBookingTime($b->getBookingTime()->format('H:i'));
            $bookingObj->setBookingStatus($b->getBookingStatus());
            $bookingObj->setCreatedAt($b->getCreatedAt());
            $bookingObj->setUpdatedAt($b->getUpdatedAt());
           $booking = $bookingObj->__toArray();
            return $booking;
        }, $bookings);
        return $bookings;
    }

    public function update(int|string $uuid, array $data): bool
    {
        try {
            $booking = BookingDal::get($uuid);
            if (!$booking) {
                return false;
            }

            $booking->setBookingDate($data['booking_date'])
                ->setBookingTime($data['booking_time'])
                ->setBookingStatus($data['booking_status'])
                ->setUpdatedAt(date(self::DATE_TIME_FORMAT));

            BookingDal::update($uuid, $booking);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function delete(int|string $uuid): bool
    {
        try {
            $booking = new BookingDal();
            $result = $booking->delete($uuid);
            return $result;
        } catch (Exception $e) {
            return false;
        }
    }
}