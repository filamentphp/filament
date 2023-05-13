<?php

namespace Filament\Context\Concerns;

trait HasMiddleware
{
    /**
     * @var array<string>
     */
    protected array $middleware = [];

    /**
     * @var array<string>
     */
    protected array $authMiddleware = [];

    /**
     * @param  array<string>  $middleware
     */
    public function middleware(array $middleware): static
    {
        $this->middleware = [
            ...$this->middleware,
            ...$middleware,
        ];

        return $this;
    }

    /**
     * @param  array<string>  $middleware
     */
    public function authMiddleware(array $middleware): static
    {
        $this->authMiddleware = [
            ...$this->authMiddleware,
            ...$middleware,
        ];

        return $this;
    }

    /**
     * @return array<string>
     */
    public function getMiddleware(): array
    {
        return [
            "context:{$this->getId()}",
            ...$this->middleware,
        ];
    }

    /**
     * @return array<string>
     */
    public function getAuthMiddleware(): array
    {
        return $this->authMiddleware;
    }
}
