<?php
namespace MyApp\Utils;

use Respect\Validation\Validator as v;
use Respect\Validation\Validatable;
use Ramsey\Uuid\Rfc4122\Validator as Rfc4122Validator;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidFactory;
use PH7\JustHttp\StatusCode;
// use Respect\Validation\Exceptions\ValidationException;
use Respect\Validation\Exceptions\NestedValidationException;

class ValidatorHelper
{
    public static function validateEmail(?string $email, bool $mandatory = true): array
    {
        return self::runValidation($email, v::email(), 'Invalid email address', $mandatory);
    }

    public static function validateUuid(?string $uuid, bool $mandatory = true): array
    {
        return self::runValidation($uuid, v::uuid(version: 4), 'Invalid UUID', $mandatory);
    }

    public static function validateUserUuid(string|int|null $uuid)
    {
        $factory = new UuidFactory();
        $factory->setValidator(new Rfc4122Validator());

        Uuid::setFactory($factory);

        if (!Uuid::isValid($uuid)) {
            // return self::runValidation($uuid, v::uuid(), 'Invalid UUID', true);
            return ['status' => false, 'message' => "Invalid userId", 'code' => StatusCode::UNPROCESSABLE_ENTITY];
        }
        return ['status' => true];
    }

    public static function validateId(string|int|null $id)
    {
        return self::runValidation($id, v::numericVal(), 'Invalid ID', true);
    }

    public static function validateString(
        ?string $value,
        string $field,
        int $min = 1,
        int $max = 255,
        bool $mandatory = true
    ): array {
        return self::runValidation(
            $value,
            v::stringType()->length($min, $max),
            "$field must be between $min and $max characters",
            $mandatory
        );
    }

    public static function validateTextArea(
        ?string $value,
        string $field,
        int $min = 3,
        bool $mandatory = true
    ): array {
        return self::runValidation(
            $value,
            v::stringType()->length($min, null),
            "$field must be at least $min characters",
            $mandatory
        );
    }

    public static function validatePassword(?string $password, bool $mandatory = true): array
    {
        return self::runValidation(
            $password,
            v::stringType()->length(6, 255),
            'Password must be at least 6 characters',
            $mandatory
        );
    }

    public static function validatePhone(?string $phone, bool $mandatory = true): array
    {
        return self::runValidation(
            $phone,
            v::stringType()->length(10, 15),
            'Phone number must be between 10 and 15 characters',
            $mandatory
        );
    }

    public static function validateAddress(?string $address, bool $mandatory = true): array
    {
        return self::runValidation(
            $address,
            v::stringType()->length(5, null),
            'Address cannot be blank and must have at least 5 characters',
            $mandatory
        );
    }

    public static function validateNumber(?string $number, bool $mandatory = true): array
    {
        return self::runValidation(
            $number,
            v::numericVal(),
            'Number must be a number',
            $mandatory
        );
    }

    public static function validateDate(?string $date, string $field, bool $mandatory = true): array
    {
        return self::runValidation(
            $date,
            v::date(),
            "$field must be a valid date",
            $mandatory
        );
    }

    public static function validateTime(?string $time, string $field, bool $mandatory = true): array
    {
        $rule = v::time()->setName($field);

        return self::runValidation(
            $time,
            $rule,
            "$field must be a valid time (e.g. 09:00, 9am, 2:30 PM)",
            $mandatory
        );
    }

    public static function validateTime24Hours(?string $time, string $field, bool $mandatory = true): array
    {
        return self::runValidation(
            $time,
            v::regex('/^(?:[01]\d|2[0-3]):[0-5]\d$/')->notEmpty(),
            "$field must be a valid time (HH:MM, 00:00 - 23:59)",
            $mandatory
        );
    }

    public static function validateFloat(?string $float, string $field, bool $mandatory = true): array
    {
        return self::runValidation(
            $float,
            v::floatVal(),
            "$field must be a decimal number",
            $mandatory
        );
    }

    public static function validateName(string $name, string $field = 'Name'): array
    {
        return self::runValidation(
            $name,
            v::notEmpty()->alpha(' ')->length(2, null),
            "$field must be at least 2 characters and contain only letters"
        );
    }

    public static function validateUsername(string $username): array
    {
        return self::runValidation(
            $username,
            v::alnum('_')->noWhitespace()->length(3, 20),
            "Username must be 3-20 characters, only letters, numbers, and underscores, no spaces"
        );
    }

    public static function validateNotNull(mixed $value, string $field = 'Field'): array
    {
        return self::runValidation(
            $value,
            v::notEmpty(),
            "$field cannot be empty"
        );
    }

    /**
     * 🔑 Internal reusable runner
     */
    public static function runValidation(mixed $value, Validatable $validator, string $message, bool $mandatory = true): array
    {
        // Use the null coalescing operator to handle non-string values safely.
        // Also, use the `empty()` function for a more reliable check for empty strings, nulls, etc.
        $trimmedValue = is_string($value) ? trim($value) : $value;

        // Check for a non-mandatory, empty value.
        if (!$mandatory && empty($trimmedValue)) {
            return ['status' => true, 'value' => $trimmedValue ?? ''];
        }

        // If the value is mandatory, or a non-mandatory field with a value, run the validator.
        try {
            $validator->assert($trimmedValue); // throws ValidationException if invalid
            return ['status' => true, 'value' => $trimmedValue];
        } catch (NestedValidationException $e) {
            // $messages = array_values($e->getMessages());
            return [
                'status' => false,
                'message' => $message,
                'details' => $e->getMessages(),
                'value' => $trimmedValue ?? '',
            ];
        }
    }
}