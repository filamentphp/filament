<?php

namespace Filament\Exceptions;

use Exception;
use Throwable;

class TenantAuthorizationException extends Exception
{
    final public function __construct(string $message = '', int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public static function make(): static
    {
        return new static('User not allowed to access tenant.');
    }
}
