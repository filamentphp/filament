<?php

namespace Filament\Support\Components;

use Closure;
use Exception;
use Filament\Support\Concerns\Configurable;
use Filament\Support\Concerns\EvaluatesClosures;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Traits\Macroable;
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

    protected string $view;

    protected array $viewData = [];

    protected string $viewIdentifier;

    protected static array $propertyCache = [];

    protected static array $methodCache = [];

    public function view(string $view, array $viewData = []): static
    {
        $this->view = $view;

        if ($viewData !== []) {
            $this->viewData($viewData);
        }

        return $this;
    }

    protected function extractPublicProperties(): array
    {
        $class = get_class($this);

        if (! isset(static::$propertyCache[$class])) {
            $reflection = new ReflectionClass($this);

            static::$propertyCache[$class] = collect($reflection->getProperties(ReflectionProperty::IS_PUBLIC))
                ->filter(fn (ReflectionProperty $property): bool => ! $property->isStatic())
                ->map(fn (ReflectionProperty $property): string => $property->getName())
                ->all();
        }

        $values = [];

        foreach (static::$propertyCache[$class] as $property) {
            $values[$property] = $this->{$property};
        }

        return $values;
    }

    protected function extractPublicMethods(): array
    {
        $class = get_class($this);

        if (! isset(static::$methodCache[$class])) {
            $reflection = new ReflectionClass($this);

            static::$methodCache[$class] = array_map(
                fn (ReflectionMethod $method): string => $method->getName(),
                $reflection->getMethods(ReflectionMethod::IS_PUBLIC),
            );
        }

        $values = [];

        foreach (static::$methodCache[$class] as $method) {
            $values[$method] = Closure::fromCallable([$this, $method]);
        }

        return $values;
    }

    public function viewData(array $data): static
    {
        $this->viewData = array_merge($this->viewData, $data);

        return $this;
    }

    public function getView(): string
    {
        if (! isset($this->view)) {
            throw new Exception('Class [' . static::class . '] extends [' . ViewComponent::class . '] but does not have a [$view] property defined.');
        }

        return $this->view;
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
