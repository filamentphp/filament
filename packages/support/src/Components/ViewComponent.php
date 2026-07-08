<?php

namespace Filament\Support\Components;

use Closure;
use Filament\Support\Components\Contracts\HasEmbeddedView;
use Filament\Support\View\ComponentAttributeBag as FilamentComponentAttributeBag;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Arr;
use Illuminate\Support\HtmlString;
use Illuminate\View\FileViewFinder;
use LogicException;

abstract class ViewComponent extends Component implements Htmlable
{
    protected string $view;

    /**
     * @var view-string | Closure | null
     */
    protected string | Closure | null $defaultView = null;

    /**
     * @var array<array<string, mixed> | Closure>
     */
    protected array $viewData = [];

    protected string $viewIdentifier;

    /**
     * @var array<view-string, View>
     */
    protected array $viewInstances = [];

    protected ?string $publishedViewOverrideCheckPath = null;

    /**
     * @var array<string, bool>
     */
    private static array $hasPublishedEmbeddedViewOverrideCache = [];

    /**
     * @param  view-string | null  $view
     * @param  array<string, mixed> | Closure  $viewData
     */
    public function view(?string $view, array | Closure $viewData = []): static
    {
        if ($view === null) {
            return $this;
        }

        $this->view = $view;

        if (filled($viewData)) {
            $this->viewData($viewData);
        }

        return $this;
    }

    /**
     * @param  view-string | Closure | null  $view
     */
    public function defaultView(string | Closure | null $view): static
    {
        $this->defaultView = $view;

        return $this;
    }

    /**
     * @return array<string, Closure>
     */
    protected function extractPublicMethods(): array
    {
        return ComponentManager::resolve()->extractPublicMethods($this);
    }

    /**
     * @param  array<string, mixed> | Closure  $data
     */
    public function viewData(array | Closure $data): static
    {
        $this->viewData[] = $data;

        return $this;
    }

    /**
     * @return view-string
     */
    public function getView(): string
    {
        if (isset($this->view)) {
            return $this->view;
        }

        if (filled($defaultView = $this->getDefaultView())) {
            return $defaultView;
        }

        throw new LogicException('Class [' . static::class . '] extends [' . ViewComponent::class . '] but does not have a [$view] property defined.');
    }

    public function hasView(): bool
    {
        return isset($this->view) || $this->getDefaultView();
    }

    /**
     * @return view-string | null
     */
    public function getDefaultView(): ?string
    {
        return $this->evaluate($this->defaultView);
    }

    /**
     * @return array<string, mixed>
     */
    public function getViewData(): array
    {
        return Arr::mapWithKeys(
            $this->viewData,
            fn (mixed $data): array => $this->evaluate($data) ?? [],
        );
    }

    public function toHtml(): string
    {
        if ((! ($this instanceof HasEmbeddedView)) || $this->hasView()) {
            return $this->render()->render();
        }

        $publishedViewOverrideCheckPath = $this->getPublishedViewOverrideCheckPath();

        if (filled($publishedViewOverrideCheckPath) && static::hasPublishedEmbeddedViewOverride($publishedViewOverrideCheckPath)) {
            static::registerPublishedEmbeddedViewOverrideNamespace($publishedViewOverrideCheckPath);

            return $this->renderView($publishedViewOverrideCheckPath)->render();
        }

        return $this->toEmbeddedHtml();
    }

    public function getPublishedViewOverrideCheckPath(): ?string
    {
        return $this->publishedViewOverrideCheckPath;
    }

    public static function hasPublishedEmbeddedViewOverride(string $view): bool
    {
        return self::$hasPublishedEmbeddedViewOverrideCache[$view] ??= self::checkForPublishedEmbeddedViewOverride($view);
    }

    protected static function checkForPublishedEmbeddedViewOverride(string $view): bool
    {
        if (! str_contains($view, '::')) {
            return false;
        }

        [$namespace, $name] = explode('::', $view, 2);

        return file_exists(resource_path('views/vendor/' . $namespace . '/' . str_replace('.', '/', $name) . '.blade.php'));
    }

    protected static function registerPublishedEmbeddedViewOverrideNamespace(string $view): void
    {
        if (! str_contains($view, '::')) {
            return;
        }

        [$namespace] = explode('::', $view, 2);

        $factory = app('view');

        $overridePath = resource_path('views/vendor/' . $namespace);

        // Laravel only registers the published override path as a namespace hint if the
        // directory already exists when the `view` factory is first resolved. Livewire 4
        // resolves the factory early during boot, so an override published afterwards is
        // never picked up. Registering the location here keeps overrides working regardless
        // of when the `view` factory was resolved.
        $finder = $factory->getFinder();

        if ($finder instanceof FileViewFinder && in_array($overridePath, $finder->getHints()[$namespace] ?? [], true)) {
            return;
        }

        $factory->prependNamespace($namespace, $overridePath);
    }

    public function toHtmlString(): ?HtmlString
    {
        $html = $this->toHtml();

        if (blank($html)) {
            return null;
        }

        return new HtmlString($html);
    }

    /**
     * @return array<string, mixed>
     */
    public function getExtraViewData(): array
    {
        return [];
    }

    public function render(): View
    {
        return $this->renderView($this->getView());
    }

    /**
     * @param  view-string  $view
     */
    protected function renderView(string $view): View
    {
        $this->viewInstances[$view] ??= view($view, [
            ...$this->extractPublicMethods(),
            ...(isset($this->viewIdentifier) ? [$this->viewIdentifier => $this] : []),
        ]);

        return $this->viewInstances[$view]->with([
            'attributes' => new FilamentComponentAttributeBag,
            ...$this->getExtraViewData(),
            ...$this->getViewData(),
        ]);
    }
}
