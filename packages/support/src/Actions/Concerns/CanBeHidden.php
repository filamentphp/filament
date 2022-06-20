<?php

namespace Filament\Support\Actions\Concerns;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

trait CanBeHidden
{
    protected $authorization = null;

    protected bool | Closure $isHidden = false;

    protected bool | Closure $isVisible = true;

    public function authorize($abilities, Model | array | null $arguments = null): static
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

    public function authorizeAny(string | array $abilities, Model | array | null $arguments = null): static
    {
        $this->authorization = [
            'type' => 'any',
            'abilities' => Arr::wrap($abilities),
            'arguments' => Arr::wrap($arguments),
        ];

        return $this;
    }

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

    public function isAuthorized(): bool
    {
        $user = auth()->user();

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
            'all' => $user->can($abilities, $arguments),
            'any' => $user->canAny($abilities, $arguments),
            default => false,
        };
    }
}
