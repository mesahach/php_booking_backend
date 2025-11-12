<?php
namespace MyApp\Enums;

enum BookingActions: string
{
    case CREATE = 'create';
    case GET = 'read';
    case GET_ALL = 'all';
    case UPDATE = 'update';
    case DELETE = 'delete';
}
