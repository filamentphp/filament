<?php

namespace Filament\Auth\Events;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Queue\SerializesModels;

class EmailChanged
{
    use SerializesModels;

    public function __construct(
        protected Authenticatable $user,
        protected string $previousEmail,
    ) {}

    public function getUser(): Authenticatable
    {
        return $this->user;
    }

    public function getPreviousEmail(): string
    {
        return $this->previousEmail;
    }
}
