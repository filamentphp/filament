<?php

namespace Filament\Exceptions;

use Exception;
use Throwable;

class NonTenantModelException extends Exception
{
    final public function __construct(string $message = '', int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public static function make(): static
    {
        return new static('The authenticated panel user is not an instance of HasTenants. Please ensure the model implements the HasTenants contract.');
    }
}
