<?php

namespace Negbross\LaravelDuitku\Exceptions;

use Exception;

class MissingParamaterException extends Exception
{
    protected $message = 'Missing parameter data required.';
}
