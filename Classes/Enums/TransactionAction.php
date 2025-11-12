<?php
namespace MyApp\Enums;

enum TransactionAction: string
{
    case CREATE = 'create';
    case VERIFY = 'verify';
    case SEARCH = 'search';
    case GET5DATA = 'get5data';
    case DELETE = 'delete';
    case RECENT = 'recent';
    case UPDATE = 'update';
}
