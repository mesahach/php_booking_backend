<?php 
namespace MyApp\Validation\Exception;

class UserValidationException extends \RuntimeException//\Exception
{
    public function __construct($message, $code = 0, $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
