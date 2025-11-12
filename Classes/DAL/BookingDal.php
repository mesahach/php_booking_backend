<?php
declare(strict_types=1);

namespace MyApp\DAL;

use RedBeanPHP\R;
use RedBeanPHP\OODBBean;
use RedBeanPHP\SimpleModel;
use MyApp\Entity\BookingModelEntity;
use \RedBeanPHP\RedException\SQL as SQLException;
use PH7\JustHttp\StatusCode;


final class BookingDal extends SimpleModel
{
    private const TABLE_NAME = 'bookings';


    public static function create(BookingModelEntity $data): int|string|false
    {
        $booking = R::dispense(self::TABLE_NAME);
        $booking->uuid = $data->getUuid();
        $booking->celebrity_uuid = $data->getCelebrityUuid();
        $booking->user_uuid = $data->getUserUuid();
        $booking->booking_date = $data->getBookingDate();
        $booking->booking_time = $data->getBookingTime();
        $booking->booking_status = $data->getBookingStatus();
        $booking->created_at = $data->getCreatedAt();
        $booking->updated_at = $data->getUpdatedAt();
        try {
            $id = R::store($booking);
            // Ensure unique index on uuid
            R::exec('ALTER TABLE ' . self::TABLE_NAME . ' ADD UNIQUE (uuid)');
        } catch (SQLException $e) {
            return false;
        } finally {
            R::close();
        }
        return $id;
    }

    public static function get(int|string $uuid): ?BookingModelEntity
    {
        try {
            $booking = is_numeric($uuid) ? R::load(self::TABLE_NAME, $uuid) : R::findOne(self::TABLE_NAME, 'uuid = :uuid', ['uuid' => $uuid]);

            return $booking ? (new BookingModelEntity())->__fromArray($booking->export()) : null;
        } catch (SQLException $e) {
            return null;
        } finally {
            R::close();
        }
    }

    public static function getMyBookings(int|string $uuid): array
    {
        try {
            $beans = R::findAll(self::TABLE_NAME, "user_uuid = ?", [$uuid]);
            return array_map(function($b): BookingModelEntity {
                unset($b->user_uuid);
                return (new BookingModelEntity())->__fromArray($b->export());
            }, $beans);
        } finally {
            R::close();
        }
    }

    public static function getAll(): array
    {
        $bookings = R::findAll(self::TABLE_NAME);
        return $bookings;
    }

    public static function update(int|string $uuid, BookingModelEntity $data): bool
    {
        $user = R::findOne(self::TABLE_NAME, 'uuid = :uuid', ['uuid' => $uuid]);

        $celebrityUuid = $data->getCelebrityUuid();
        if ($celebrityUuid) {
            $user->celebrity_uuid = $celebrityUuid;
        }
        $bookingDate = $data->getBookingDate();
        if ($bookingDate) {
            $user->booking_date = $bookingDate;
        }
        $bookingTime = $data->getBookingTime();
        if ($bookingTime) {
            $user->booking_time = $bookingTime;
        }
        $bookingStatus = $data->getBookingStatus();
        if ($bookingStatus) {
            $user->booking_status = $bookingStatus;
        }
        try {
            $id = R::store($user);
            R::close();
            return true;
        } catch (SQLException $e) {
            return false;
        } finally {
            R::close();
        }
    }

    public function delete(int|string $uuid): bool
    {
        try {
            $booking = R::findOne(self::TABLE_NAME, 'uuid = :uuid', ['uuid' => $uuid]);
            if (!$booking) {
                return false;
            }

           $result = (bool) R::trash($booking);
            return $result;
        } catch (SQLException $e) {
            return false;
        }
    }
}