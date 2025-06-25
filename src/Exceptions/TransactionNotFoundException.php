<?php

namespace Negbross\LaravelDuitku\Exceptions;

use Exception;

class TransactionNotFoundException extends Exception
{
    protected $message = 'Transaction not found.';
}
