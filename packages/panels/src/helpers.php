<?php

namespace Filament;

use Filament\Facades\Filament;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\Access\Response;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;

if (! function_exists('Filament\authorize')) {
    /**
     * @param array<mixed> $arguments
     *
     * @throws AuthorizationException
     */
    function authorize(string $action, Model | string $model, bool $shouldCheckPolicyExistence = true, array $arguments = []): Response
    {
        $user = Filament::auth()->user();

        if (! $shouldCheckPolicyExistence) {
            return Gate::forUser($user)->authorize($action, [$model, ...$arguments]);
        }

        $policy = Gate::getPolicyFor($model);

        if (
            ($policy === null) ||
            (! method_exists($policy, $action))
        ) {
            /** @var bool | Response | null $response */
            $response = invade(Gate::forUser($user))->callBeforeCallbacks( /** @phpstan-ignore-line */
                $user,
                $action,
                [$model, ...$arguments]
            );

            if ($response === false) {
                throw new AuthorizationException;
            }

            if (! $response instanceof Response) {
                return Response::allow();
            }

            return $response->authorize();
        }

        return Gate::forUser($user)->authorize($action, [$model, ...$arguments]);
    }
}
