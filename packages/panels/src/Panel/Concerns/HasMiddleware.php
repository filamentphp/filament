<?php

namespace Filament\Panel\Concerns;

use Filament\Http\Middleware\IdentifyTenant;
use Livewire\Livewire;

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
     * @var array<string>
     */
    protected array $tenantMiddleware = [];

    /**
     * @var array<string>
     */
    protected array $livewirePersistentMiddleware = [];

    /**
     * @param  array<string>  $middleware
     */
    public function middleware(array $middleware, bool $isPersistent = false): static
    {
        $this->middleware = [
            ...$this->middleware,
            ...$middleware,
        ];

        if ($isPersistent) {
            $this->persistentMiddleware($middleware);
        }

        return $this;
    }

    /**
     * @param  array<string>  $middleware
     */
    public function authMiddleware(array $middleware, bool $isPersistent = false): static
    {
        $this->authMiddleware = [
            ...$this->authMiddleware,
            ...$middleware,
        ];

        if ($isPersistent) {
            $this->persistentMiddleware($middleware);
        }

        return $this;
    }

    /**
     * @param  array<string>  $middleware
     */
    public function tenantMiddleware(array $middleware, bool $isPersistent = false): static
    {
        $this->tenantMiddleware = [
            ...$this->tenantMiddleware,
            ...$middleware,
        ];

        if ($isPersistent) {
            $this->persistentMiddleware($middleware);
        }

        return $this;
    }

    /**
     * @param  array<string>  $middleware
     */
    public function persistentMiddleware(array $middleware): static
    {
        $this->livewirePersistentMiddleware = [
            ...$this->livewirePersistentMiddleware,
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
            "panel:{$this->getId()}",
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

    /**
     * @return array<string>
     */
    public function getTenantMiddleware(): array
    {
        return [
            IdentifyTenant::class,
            ...$this->tenantMiddleware,
        ];
    }

    protected function registerLivewirePersistentMiddleware(): void
    {
        Livewire::addPersistentMiddleware($this->livewirePersistentMiddleware);

        $this->livewirePersistentMiddleware = [];
    }
}
