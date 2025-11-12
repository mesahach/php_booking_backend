<?php
namespace MyApp\Enums;

enum ProductAction: string
{
    case FEATURED = 'featured';
    case SEARCH = 'search';
    case CATEGORY = 'category';
    case CREATE = 'create';
    case UPDATE = 'update';
    case DELETE = 'delete';
    case GET = 'get';
    case GETALL = 'getall';
}
