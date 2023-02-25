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
        $this->middleware = array_merge(
            $this->middleware,
            $middleware,
        );

        return $this;
    }

    /**
     * @param  array<string>  $middleware
     */
    public function authMiddleware(array $middleware): static
    {
        $this->authMiddleware = array_merge(
            $this->authMiddleware,
            $middleware,
        );

        return $this;
    }

    /**
     * @return array<string>
     */
    public function getMiddleware(): array
    {
        return array_merge(
            ["context:{$this->getId()}"],
            $this->middleware,
        );
    }

    /**
     * @return array<string>
     */
    public function getAuthMiddleware(): array
    {
        return $this->authMiddleware;
    }
}
