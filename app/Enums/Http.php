<?php
namespace App\Enums;

enum Http: int
{
    case OK = 200;
    case CREATED = 201;
    case UNAUTHORIZED = 401;
    case FORBIDDEN = 403;
    case VALIDATION_ERROR = 422;
    case SERVER_ERROR = 500;
    case NOT_FOUND = 404;
    case BAD_REQUEST = 400;
}
