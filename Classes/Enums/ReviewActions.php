<?php
namespace MyApp\Enums;

enum ReviewActions: string
{
    case CREATE = 'create';
    case GET = 'read';
    case GET_ALL = 'readAll';
    case UPDATE = 'update';
    case DELETE = 'delete';
}
