<?php

namespace Negbross\LaravelDuitku\Exceptions;

use Exception;

class PaymentMethodUnavailableException extends Exception
{
    protected $message = 'Error payment method unavailabe';
}
