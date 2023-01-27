<?php

namespace Filament\Actions\Concerns;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Gate;

trait CanBeHidden
{
    protected mixed $authorization = null;

    protected bool | Closure $isHidden = false;

    protected bool | Closure $isVisible = true;

    /**
     * @param  Model | array<mixed> | null  $arguments
     */
    public function authorize(mixed $abilities, Model | array | null $arguments = null): static
    {
        if (is_string($abilities) || is_array($abilities)) {
            $this->authorization = [
                'type' => 'all',
                'abilities' => Arr::wrap($abilities),
                'arguments' => Arr::wrap($arguments),
            ];
        } else {
            $this->authorization = $abilities;
        }

        return $this;
    }

    /**
     * @param  string | array<string>  $abilities
     * @param  Model | array<mixed> | null  $arguments
     */
    public function authorizeAny(string | array $abilities, Model | array | null $arguments = null): static
    {
        $this->authorization = [
            'type' => 'any',
            'abilities' => Arr::wrap($abilities),
            'arguments' => Arr::wrap($arguments),
        ];

        return $this;
    }

    /**
     * @param  array<mixed>  $arguments
     * @return array<mixed>
     */
    protected function parseAuthorizationArguments(array $arguments): array
    {
        return $arguments;
    }

    public function hidden(bool | Closure $condition = true): static
    {
        $this->isHidden = $condition;

        return $this;
    }

    public function visible(bool | Closure $condition = true): static
    {
        $this->isVisible = $condition;

        return $this;
    }

    public function isAuthorized(): bool
    {
        if ($this->authorization === null) {
            return true;
        }

        if (! is_array($this->authorization)) {
            return (bool) $this->evaluate($this->authorization);
        }

        $abilities = $this->authorization['abilities'] ?? [];
        $arguments = $this->parseAuthorizationArguments($this->authorization['arguments'] ?? []);
        $type = $this->authorization['type'] ?? null;

        return match ($type) {
            'all' => Gate::check($abilities, $arguments),
            'any' => Gate::any($abilities, $arguments),
            default => false,
        };
    }

    public function isHidden(): bool
    {
        if ($this->evaluate($this->isHidden)) {
            return true;
        }

        if (! $this->evaluate($this->isVisible)) {
            return true;
        }

        return ! $this->isAuthorized();
    }

    public function isVisible(): bool
    {
        return ! $this->isHidden();
    }
}
