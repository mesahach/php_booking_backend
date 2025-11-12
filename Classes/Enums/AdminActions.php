<?php
namespace MyApp\Enums;

enum AdminActions: string
{
    case CREATE = 'create';
    case GET = 'read';
    case PROFILE = 'profile';
    case COUNTDATA = "countData";
    case GET_ALL = 'all';
    case UPDATE = 'update';
    case DELETE = 'delete';
    case LOGIN = 'login';
    case LOGOUT = 'logout';
    case IS_LOGGED_IN = 'isLoggedIn';
    case LOGIN_JWT = 'login_admin_jwt_Value';
}
