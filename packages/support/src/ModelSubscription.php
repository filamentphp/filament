<?php

namespace Filament\Support;

use BackedEnum;
use Closure;
use Filament\Support\Components\Component;
use Filament\Support\Enums\ModelEvent;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

class ModelSubscription extends Component
{
    /**
     * @var ?class-string<Model>
     */
    protected ?string $model = null;

    protected ?string $modelNamespace = null;

    protected bool $isForAllModels = false;

    /**
     * @var array<class-string<Model>>
     */
    protected array $exceptModels = [];

    /**
     * @var ?array<BackedEnum | string>
     */
    protected ?array $onlyEvents = null;

    /**
     * @var array<BackedEnum | string>
     */
    protected array $exceptEvents = [];

    protected ?Closure $afterModelSelectorChanged = null;

    /**
     * @param  ?class-string<Model>  $model
     */
    public function __construct(?string $model = null)
    {
        $this->model($model);
    }

    public function __clone(): void
    {
        $this->afterModelSelectorChanged = null;
    }

    /**
     * @param  ?class-string<Model>  $model
     */
    public static function make(?string $model = null): static
    {
        $static = app(static::class, ['model' => $model]);
        $static->configure();

        return $static;
    }

    /**
     * @param  ?class-string<Model>  $model
     */
    public function model(?string $model): static
    {
        $this->model = $model;
        $this->modelNamespace = null;
        $this->isForAllModels = false;

        $this->afterModelSelectorChanged?->__invoke();

        return $this;
    }

    public function modelsInNamespace(?string $namespace): static
    {
        if ($namespace !== null) {
            $namespace = trim($namespace, '\\');

            if ($namespace === '') {
                throw new InvalidArgumentException('The model namespace must not be empty.');
            }
        }

        $this->modelNamespace = $namespace;
        $this->model = null;
        $this->isForAllModels = false;

        $this->afterModelSelectorChanged?->__invoke();

        return $this;
    }

    public function allModels(bool $condition = true): static
    {
        $this->isForAllModels = $condition;

        if ($condition) {
            $this->model = null;
            $this->modelNamespace = null;
        }

        $this->afterModelSelectorChanged?->__invoke();

        return $this;
    }

    /**
     * @internal
     */
    public function afterModelSelectorChanged(?Closure $callback): static
    {
        $this->afterModelSelectorChanged = $callback;

        return $this;
    }

    /**
     * @param  array<class-string<Model>>  $models
     */
    public function exceptModels(array $models): static
    {
        $this->exceptModels = $models;

        return $this;
    }

    /**
     * @param  ?array<BackedEnum | string>  $events
     */
    public function onlyEvents(?array $events): static
    {
        $this->onlyEvents = $events;

        return $this;
    }

    /**
     * @param  array<BackedEnum | string>  $events
     */
    public function exceptEvents(array $events): static
    {
        $this->exceptEvents = $events;

        return $this;
    }

    /**
     * @return ?class-string<Model>
     */
    public function getModel(): ?string
    {
        return $this->model;
    }

    public function getModelNamespace(): ?string
    {
        return $this->modelNamespace;
    }

    public function isForAllModels(): bool
    {
        return $this->isForAllModels;
    }

    public function getKey(): ?string
    {
        if (($model = $this->getModel()) !== null) {
            return $model;
        }

        if (($namespace = $this->getModelNamespace()) !== null) {
            return "{$namespace}\\*";
        }

        if ($this->isForAllModels()) {
            return '*';
        }

        return null;
    }

    /**
     * @param  class-string<Model>  $model
     */
    public function matchesModel(string $model): bool
    {
        if (in_array($model, $this->exceptModels, strict: true)) {
            return false;
        }

        return $this->isForAllModels()
            || ($this->getModel() === $model)
            || ((($namespace = $this->getModelNamespace()) !== null) && str_starts_with($model, "{$namespace}\\"));
    }

    /**
     * @return array<BackedEnum | string>
     */
    public function getEvents(): array
    {
        $except = array_map(
            static fn (BackedEnum | string $event): string => ($event instanceof BackedEnum) ? (string) $event->value : $event,
            $this->exceptEvents,
        );

        return array_values(array_filter(
            $this->onlyEvents ?? ModelEvent::cases(),
            static fn (BackedEnum | string $event): bool => ! in_array(
                ($event instanceof BackedEnum) ? (string) $event->value : $event,
                $except,
                strict: true,
            ),
        ));
    }
}
