<?php
declare(strict_types = 1);

namespace App\FormBuilder;

enum FormMethod: string{
    case GET = 'get';
    case POST = 'post';
    case PUT = 'put';
    case DELETE = 'delete';
    case PATCH = 'patch';
}