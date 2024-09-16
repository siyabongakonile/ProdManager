<?php 
declare(strict_types = 1);

namespace App\FormBuilder;

enum InputType: string{
    case TEXT     = 'text';
    case PASSWORD = 'password';
    case CHECKBOX = 'checkbox';
    case RADIO    = 'radio';
    case NUMBER   = 'number';
    case DATE     = 'date';
    case TIME     = 'time';
    case EMAIL    = 'email';
    case URL      = 'url';
    case FILE     = 'file';
    case COLOR    = 'color';
    case RANGE    = 'range';
    case HIDDEN   = 'hidden';
    case SUBMIT   = 'submit';
    case RESET    = 'reset';
    case IMAGE    = 'image';
    case MONTH    = 'month';
    case WEEK     = 'week';
    case DATETIME = 'datetime';
    case DATETIME_LOCAL = 'datetime-local';
    case SEARCH   = 'search';
    case TEL      = 'tel';
}