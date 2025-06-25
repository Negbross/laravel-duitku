<?php

namespace Negbross\LaravelDuitku\Exceptions;

use Exception;

class InvalidSignatureException extends Exception
{
    protected $message = 'Error invalid signature.';
}
