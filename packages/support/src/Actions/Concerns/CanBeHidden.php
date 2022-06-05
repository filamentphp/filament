<?php

namespace Filament\Support\Actions\Concerns;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

trait CanBeHidden
{
    protected array $authorizations = [];

    protected bool | Closure $isHidden = false;

    protected bool | Closure $isVisible = true;

    public function authorize($abilities, Model | array | null $arguments = null): static
    {
        if (is_string($abilities) || is_array($abilities)) {
            $this->authorizations[] = [
                'type' => 'all',
                'abilities' => Arr::wrap($abilities),
                'arguments' => Arr::wrap($arguments),
            ];
        } else {
            $this->authorizations[] = $abilities;
        }

        return $this;
    }

    public function authorizeAny(string | array $abilities, Model | array | null $arguments = null): static
    {
        $this->authorizations[] = [
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
        foreach ($this->authorizations as $authorization) {
            if (! $this->checkAuthorization($authorization)) {
                return false;
            }
        }

        return true;
    }

    protected function checkAuthorization($authorization): bool
    {
        $user = auth()->user();

        if (! is_array($authorization)) {
            return $this->evaluate($authorization);
        }

        $abilities = $authorization['abilities'] ?? [];
        $arguments = $this->parseAuthorizationArguments($authorization['arguments'] ?? []);
        $type = $authorization['type'] ?? null;

        if ($type === 'all') {
            return $user->can($abilities, $arguments);
        }

        if ($type === 'any') {
            return $user->canAny($abilities, $arguments);
        }

        return false;
    }
}
