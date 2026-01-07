<?php

namespace Filament;

use BackedEnum;
use Filament\Facades\Filament;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\Access\Response;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Gate;
use LogicException;
use UnitEnum;

if (! function_exists('Filament\authorize')) {
    /**
     * @param  Model|class-string<Model>  $model
     *
     * @throws AuthorizationException
     */
    function authorize(UnitEnum | string $action, Model | string $model, bool $shouldCheckPolicyExistence = true): Response
    {
        return get_authorization_response($action, $model, $shouldCheckPolicyExistence)->authorize();
    }
}

if (! function_exists('Filament\get_authorization_response')) {
    /**
     * @param  Model|class-string<Model>  $model
     */
    function get_authorization_response(UnitEnum | string $action, Model | string $model, bool $shouldCheckPolicyExistence = true): Response
    {
        $user = Filament::auth()->user();

        $actionValue = match (true) {
            $action instanceof BackedEnum => $action->value,
            $action instanceof UnitEnum => $action->name,
            default => $action,
        };

        if (! $shouldCheckPolicyExistence) {
            if (
                Filament::isAuthorizationStrict()
                && (! Gate::forUser($user)->has($action))
                && (
                    blank($policyClass = Gate::getPolicyFor($model))
                    || (! method_exists($policyClass, $actionValue))
                )
            ) {
                throw new LogicException(blank($policyClass)
                    ? "Strict authorization mode is enabled, but no ability [{$actionValue}] or policy with method [{$actionValue}()] was found for [{$model}]."
                    : "Strict authorization mode is enabled, but no ability [{$actionValue}] or [{$actionValue}()] method was found on [{$policyClass}].");
            }

            return Gate::forUser($user)->inspect($action, Arr::wrap($model));
        }

        $policy = Gate::getPolicyFor($model);

        if (filled($policy) && method_exists($policy, $actionValue)) {
            return Gate::forUser($user)->inspect($action, Arr::wrap($model));
        }

        if (Filament::isAuthorizationStrict()) {
            $policyClass = match (true) {
                is_string($policy) => $policy,
                is_object($policy) => $policy::class,
                default => null,
            };

            throw new LogicException(blank($policyClass)
                ? "Strict authorization mode is enabled, but no policy was found for [{$model}]."
                : "Strict authorization mode is enabled, but no [{$actionValue}()] method was found on [{$policyClass}].");
        }

        /** @var bool | Response | null $response */
        $response = invade(Gate::forUser($user))->callBeforeCallbacks( /** @phpstan-ignore-line */
            $user,
            $action,
            [$model],
        );

        if ($response === false) {
            return Response::deny();
        }

        if (! $response instanceof Response) {
            return Response::allow();
        }

        return $response;
    }
}
