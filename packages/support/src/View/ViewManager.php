<?php

namespace Filament\Support\View;

use Closure;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Arr;
use Illuminate\Support\HtmlString;

use function Filament\Support\is_app_url;

class ViewManager
{
    /**
     * @var array<string, array<string, array<Closure>>>
     */
    protected array $renderHooks = [];

    protected bool $hasSpaMode = false;

    protected bool $hasSpaPrefetching = false;

    /**
     * @var array<string>
     */
    protected array $spaModeUrlExceptions = [];

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
    public function hasRenderHook(string $name, string | array | null $scopes = null): bool
    {
        if (! isset($this->renderHooks[$name])) {
            return false;
        }

        if (isset($this->renderHooks[$name]['']) && count($this->renderHooks[$name][''])) {
            return true;
        }

        $scopes = Arr::wrap($scopes);

        foreach ($scopes as $scopeName) {
            if (isset($this->renderHooks[$name][$scopeName]) && count($this->renderHooks[$name][$scopeName])) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param  string | array<string> | null  $scopes
     * @param  array<string, mixed>  $data
     */
    public function renderHook(string $name, string | array | null $scopes = null, array $data = []): Htmlable
    {
        $renderedHooks = [];

        $scopes = Arr::wrap($scopes);

        $renderHook = function (callable $hook) use (&$renderedHooks, $scopes, $data): ?string {
            $hookId = spl_object_id($hook);

            if (in_array($hookId, $renderedHooks)) {
                return null;
            }

            $renderedHooks[] = $hookId;

            $result = app()->call($hook, ['data' => $data, 'scopes' => $scopes]);

            return (string) $result;
        };

        $hooks = array_map(
            $renderHook,
            $this->renderHooks[$name][''] ?? [],
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

    public function spa(bool $condition = true, bool $hasPrefetching = false): void
    {
        $this->hasSpaMode = $condition;
        $this->hasSpaPrefetching = $hasPrefetching;
    }

    /**
     * @param  array<string>  $exceptions
     */
    public function spaUrlExceptions(array $exceptions): void
    {
        $this->spaModeUrlExceptions = [
            ...$this->spaModeUrlExceptions,
            ...$exceptions,
        ];
    }

    public function hasSpaMode(?string $url = null): bool
    {
        if (! $this->hasSpaMode) {
            return false;
        }

        if (blank($url)) {
            return true;
        }

        if (count($this->spaModeUrlExceptions) && str($url)->is($this->spaModeUrlExceptions)) {
            return false;
        }

        return is_app_url($url);
    }

    public function hasSpaPrefetching(): bool
    {
        return $this->hasSpaPrefetching;
    }
}
