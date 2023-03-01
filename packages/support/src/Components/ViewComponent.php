<?php

namespace Filament\Support\Components;

use Closure;
use Exception;
use Filament\Support\Concerns\Configurable;
use Filament\Support\Concerns\EvaluatesClosures;
use Filament\Support\Concerns\Macroable;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Traits\Tappable;
use Illuminate\View\ComponentAttributeBag;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;

abstract class ViewComponent extends Component implements Htmlable
{
    use Configurable;
    use EvaluatesClosures;
    use Macroable;
    use Tappable;

    /**
     * @var view-string
     */
    protected string $view;

    /**
     * @var view-string | Closure | null
     */
    protected string | Closure | null $defaultView = null;

    /**
     * @var array<string, mixed>
     */
    protected array $viewData = [];

    protected string $viewIdentifier;

    /**
     * @var array<string, array<string>>
     */
    protected static array $propertyCache = [];

    /**
     * @var array<string, array<string>>
     */
    protected static array $methodCache = [];

    /**
     * @param  view-string | null  $view
     */
    public function view(?string $view): static
    {
        if ($view === null) {
            return $this;
        }

        $this->view = $view;

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
     * @return array<string, mixed>
     */
    protected function extractPublicProperties(): array
    {
        if (! isset(static::$propertyCache[static::class])) {
            $reflection = new ReflectionClass($this);

            static::$propertyCache[static::class] = collect($reflection->getProperties(ReflectionProperty::IS_PUBLIC))
                ->filter(fn (ReflectionProperty $property): bool => ! $property->isStatic())
                ->map(fn (ReflectionProperty $property): string => $property->getName())
                ->all();
        }

        $values = [];

        foreach (static::$propertyCache[static::class] as $property) {
            $values[$property] = $this->{$property};
        }

        return $values;
    }

    /**
     * @return array<string, Closure>
     */
    protected function extractPublicMethods(): array
    {
        if (! isset(static::$methodCache[static::class])) {
            $reflection = new ReflectionClass($this);

            static::$methodCache[static::class] = array_map(
                fn (ReflectionMethod $method): string => $method->getName(),
                $reflection->getMethods(ReflectionMethod::IS_PUBLIC),
            );
        }

        $values = [];

        foreach (static::$methodCache[static::class] as $method) {
            $values[$method] = Closure::fromCallable([$this, $method]);
        }

        return $values;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function viewData(array $data): static
    {
        $this->viewData = array_merge($this->viewData, $data);

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

        throw new Exception('Class [' . static::class . '] extends [' . ViewComponent::class . '] but does not have a [$view] property defined.');
    }

    /**
     * @return view-string | null
     */
    public function getDefaultView(): ?string
    {
        return $this->evaluate($this->defaultView);
    }

    public function toHtml(): string
    {
        return $this->render()->render();
    }

    public function render(): View
    {
        return view(
            $this->getView(),
            array_merge(
                ['attributes' => new ComponentAttributeBag()],
                $this->extractPublicProperties(),
                $this->extractPublicMethods(),
                isset($this->viewIdentifier) ? [$this->viewIdentifier => $this] : [],
                $this->viewData,
            ),
        );
    }
}
