<?php
namespace MyApp\Enums;

enum UserAction: string
{
    case GET = '';
    case LOGIN = 'login';
    case LOGOUT = 'logout';
    case RESEND_EMAIL_OTP = 'resendEmailOTP';
    case VERIFY_EMAIL = "verifyEmailAddress";
    case CREATE = 'create';
    case DELETE = 'delete';
    case SOFTDELETE = 'softDelete';
    case PROFILE = 'profile';
    case SEARCH = 'search';
    case UPDATE = 'update';
    case UPDATE_PROFILE = "updateProfile";
    case CHANGE_PASSWORD = 'changePassword';
    case DEACTIVATE = 'deactivate';
    case IS_USER_EXIST = 'isUserExist';
    case IS_LOGGED_IN = 'isLoggedIn';
    case REFRESH = 'refreshToken';
}