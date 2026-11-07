<?php

namespace Filament\Tables\Columns\Concerns;

use BackedEnum;
use Closure;
use Illuminate\Auth\Access\Response;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;

trait CanBeAuthorized
{
    protected string | Closure | bool | null $authorization = null;

    public function authorize(string | BackedEnum | Closure | bool | null $ability = 'update'): static
    {
        if ($ability instanceof BackedEnum) {
            $ability = (string) $ability->value;
        }

        $this->authorization = $ability;

        return $this;
    }

    public function getAuthorizationResponse(): Response
    {
        if ($this->authorization === null) {
            $livewire = $this->getLivewire();

            if (! method_exists($livewire, 'getDefaultTableColumnAuthorizationResponse')) {
                return Response::allow();
            }

            return $livewire->getDefaultTableColumnAuthorizationResponse($this) ?? Response::allow();
        }

        if (is_string($this->authorization)) {
            $record = $this->getRecord();

            return Gate::inspect($this->authorization, ($record instanceof Model) ? [$record] : []);
        }

        $response = $this->evaluate($this->authorization);

        return match (true) {
            $response instanceof Response => $response,
            (bool) $response => Response::allow(),
            default => Response::deny(),
        };
    }

    public function isAuthorized(): bool
    {
        return $this->getAuthorizationResponse()->allowed();
    }

    public function isDisabled(): bool
    {
        if (parent::isDisabled()) {
            return true;
        }

        if (! $this->getRecord()) {
            return false;
        }

        return ! $this->isAuthorized();
    }
}
