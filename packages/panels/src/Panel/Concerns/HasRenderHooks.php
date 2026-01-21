<?php

namespace Filament\Panel\Concerns;

use Closure;
use Filament\Support\Facades\FilamentView;
use Filament\View\RenderHook;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;
use ReflectionClass;

trait HasRenderHooks
{
    /**
     * @var array<string, array<string, array<Closure>>>
     */
    protected array $renderHooks = [];

    /**
     * @var array<string, string>
     */
    protected array $renderHookDirectories = [];

    /**
     * @param  string | array<string> | null  $scopes
     */
    public function renderHook(string $name, Closure $hook, string | array | null $scopes = null): static
    {
        if (! is_array($scopes)) {
            $scopes = [$scopes];
        }

        foreach ($scopes as $scopeName) {
            $this->renderHooks[$name][$scopeName][] = $hook;
        }

        return $this;
    }

    public function discoverRenderHooks(string $in, string $for): static
    {
        $this->renderHookDirectories[$in] = $for;

        return $this;
    }

    protected function registerRenderHooks(): void
    {
        $filesystem = app(Filesystem::class);

        foreach ($this->renderHookDirectories as $in => $for) {
            if (blank($in) || blank($for)) {
                continue;
            }

            if (! $filesystem->exists($in)) {
                continue;
            }

            $namespace = str($for);

            foreach ($filesystem->allFiles($in) as $file) {
                $class = (string) $namespace
                    ->append('\\', $file->getRelativePathname())
                    ->replace([DIRECTORY_SEPARATOR, '.php'], ['\\', '']);

                if (! class_exists($class)) {
                    continue;
                }

                if ((new ReflectionClass($class))->isAbstract()) {
                    continue;
                }

                if (! is_subclass_of($class, RenderHook::class)) {
                    continue;
                }

                $hooks = $class::getRenderHooks();

                foreach (Arr::wrap($hooks) as $hook) {
                    $this->renderHook(
                        $hook,
                        fn (array $data, array $scopes) => app()->call([app($class), 'render'], ['data' => $data, 'scopes' => $scopes]),
                        $class::getScopes(),
                    );
                }
            }
        }

        foreach ($this->renderHooks as $hookName => $scopedHooks) {
            foreach ($scopedHooks as $scope => $hooks) {
                foreach ($hooks as $hook) {
                    FilamentView::registerRenderHook($hookName, $hook, $scope);
                }
            }
        }
    }
}
