<?php

namespace Filament\Support\View;

use Closure;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Arr;
use Illuminate\Support\HtmlString;

class ViewManager
{
    /**
     * @var array<string, array<string, array<Closure>>>
     */
    protected array $renderHooks = [];

    protected bool $hasSpaMode = false;

    /**
     * @param  string | array<string> | null  $scopes
     */
    public function registerRenderHook(string $name, Closure $hook, string | array | null $scopes = null): void
    {
        if (! is_array($scopes)) {
            $scopes = [$scopes];
        }

        foreach ($scopes as $scopeName) {
            $this->renderHooks[$name][$scopeName][] = $hook;
        }
    }

    /**
     * @param  string | array<string> | null  $scopes
     */
    public function renderHook(string $name, string | array | null $scopes = null): Htmlable
    {
        $renderedHooks = [];

        $scopes = Arr::wrap($scopes);

        $renderHook = function (callable $hook) use (&$renderedHooks, $scopes): ?string {
            $hookId = spl_object_id($hook);

            if (in_array($hookId, $renderedHooks)) {
                return null;
            }

            $renderedHooks[] = $hookId;

            return (string) app()->call($hook, ['scopes' => $scopes]);
        };

        $hooks = array_map(
            $renderHook,
            $this->renderHooks[$name][null] ?? [],
        );

        foreach ($scopes as $scopeName) {
            $hooks = [
                ...$hooks,
                ...array_map(
                    $renderHook,
                    $this->renderHooks[$name][$scopeName] ?? [],
                ),
            ];
        }

        return new HtmlString(implode('', $hooks));
    }

    public function spa(bool $condition = true): void
    {
        $this->hasSpaMode = $condition;
    }

    public function hasSpaMode(): bool
    {
        return $this->hasSpaMode;
    }
}
