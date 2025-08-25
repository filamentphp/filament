<?php

namespace Filament\Actions\Concerns;

use BackedEnum;
use Closure;
use Exception;
use Illuminate\Auth\Access\Response;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Gate;

trait CanBeAuthorized
{
    protected mixed $authorization = null;

    protected string | Closure | null $authorizationMessage = null;

    protected bool | Closure $hasAuthorizationTooltip = false;

    protected bool | Closure $hasAuthorizationNotification = false;

    protected bool | string | Closure | null $authorizeIndividualRecords = null;

    /**
     * @param  Model | class-string | array<mixed> | null  $arguments
     */
    public function authorize(mixed $abilities, Model | string | array | null $arguments = null): static
    {
        if ($abilities instanceof BackedEnum) {
            $abilities = $abilities->value;
        }

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
     * @param  string | BackedEnum | array<string>  $abilities
     * @param  Model | array<mixed> | null  $arguments
     */
    public function authorizeAny(string | BackedEnum | array $abilities, Model | array | null $arguments = null): static
    {
        if ($abilities instanceof BackedEnum) {
            $abilities = $abilities->value;
        }

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
        if ($record = $this->getRecord()) {
            array_unshift($arguments, $record);
        } elseif ($model = $this->getModel()) {
            array_unshift($arguments, $model);
        }

        return $arguments;
    }

    public function isAuthorized(): bool
    {
        if ($this->authorization === null) {
            return $this->getHasActionsLivewire()?->getDefaultActionAuthorizationResponse($this)?->allowed() ?? true;
        }

        if (! is_array($this->authorization)) {
            $response = $this->evaluate($this->authorization);

            return match (true) {
                $response instanceof Response => $response->allowed(),
                default => (bool) $response,
            };
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

    public function getAuthorizationResponse(): Response
    {
        if ($this->authorization === null) {
            return $this->getHasActionsLivewire()->getDefaultActionAuthorizationResponse($this) ?? Response::allow();
        }

        if (! is_array($this->authorization)) {
            $response = $this->evaluate($this->authorization);

            return match (true) {
                $response instanceof Response => $response,
                (bool) $response => Response::allow(),
                default => Response::deny(),
            };
        }

        $abilities = $this->authorization['abilities'] ?? [];
        $arguments = $this->parseAuthorizationArguments($this->authorization['arguments'] ?? []);
        $type = $this->authorization['type'] ?? null;

        foreach ($abilities as $ability) {
            $response = Gate::inspect($ability, Arr::wrap($arguments));

            if (($type === 'any') && $response->allowed()) {
                return $response;
            }

            if (($type === 'all') && $response->denied()) {
                return $response;
            }
        }

        return Response::allow();
    }

    public function getAuthorizationResponseWithMessage(): Response
    {
        $response = $this->getAuthorizationResponse();

        if ($response->allowed()) {
            return $response;
        }

        $message = $this->getAuthorizationMessage();

        if (filled($message)) {
            invade($response)->message = $message; /** @phpstan-ignore-line */

            return $response;
        }

        if (blank($response->message())) {
            throw new Exception('An authorization was denied without a message.');
        }

        return $response;
    }

    public function authorizationMessage(string | Closure | null $message): static
    {
        $this->authorizationMessage = $message;

        return $this;
    }

    public function getAuthorizationMessage(): ?string
    {
        return $this->evaluate($this->authorizationMessage);
    }

    public function authorizationTooltip(bool | Closure $condition = true): static
    {
        $this->hasAuthorizationTooltip = $condition;

        return $this;
    }

    public function authorizationNotification(bool | Closure $condition = true): static
    {
        $this->hasAuthorizationNotification = $condition;

        return $this;
    }

    public function hasAuthorizationTooltip(): bool
    {
        return (bool) $this->evaluate($this->hasAuthorizationTooltip);
    }

    public function hasAuthorizationNotification(): bool
    {
        return (bool) $this->evaluate($this->hasAuthorizationNotification);
    }

    public function isAuthorizedOrNotHiddenWhenUnauthorized(): bool
    {
        if ($this->hasAuthorizationTooltip()) {
            return true;
        }

        if ($this->hasAuthorizationNotification()) {
            return true;
        }

        return $this->isAuthorized();
    }

    public function authorizeIndividualRecords(bool | string | Closure | null $callback = true): static
    {
        $this->authorizeIndividualRecords = $callback;

        return $this;
    }

    public function getIndividualRecordAuthorizationResponse(Model $record): Response
    {
        if (is_string($this->authorizeIndividualRecords)) {
            return Gate::inspect($this->authorizeIndividualRecords, Arr::wrap($record));
        }

        $resolver = ($this->authorizeIndividualRecords instanceof Closure)
            ? $this->authorizeIndividualRecords
            : $this->getHasActionsLivewire()->getDefaultActionIndividualRecordAuthorizationResponseResolver($this);

        if (! $resolver) {
            throw new Exception('No function was passed to [authorizeIndividualRecords()].');
        }

        $response = $resolver($record);

        if ($response instanceof Response) {
            return $response;
        }

        return $response ? Response::allow() : Response::deny();
    }

    public function shouldAuthorizeIndividualRecords(): bool
    {
        return filled($this->authorizeIndividualRecords) && ($this->authorizeIndividualRecords !== false);
    }
}
