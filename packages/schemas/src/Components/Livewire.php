<?php

namespace Filament\Schemas\Components;

use Closure;
use Filament\Support\Components\Contracts\HasEmbeddedView;
use Filament\Support\View\ComponentAttributeBag as FilamentComponentAttributeBag;
use Livewire\Livewire as LivewireFacade;

class Livewire extends Component implements HasEmbeddedView
{
    protected ?string $publishedViewOverrideCheckPath = 'filament-schemas::components.livewire';

    protected bool | Closure $isLazy = false;

    /**
     * @var array<string, mixed> | Closure
     */
    protected array | Closure $data = [];

    protected string | Closure $component;

    /**
     * @param  array<string, mixed> | Closure  $data
     */
    final public function __construct(string | Closure $component, array | Closure $data = [])
    {
        $this->component($component);
        $this->data($data);
    }

    /**
     * @param  array<string, mixed> | Closure  $data
     */
    public static function make(string | Closure $component, array | Closure $data = []): static
    {
        $static = app(static::class, [
            'component' => $component,
            'data' => $data,
        ]);
        $static->configure();

        return $static;
    }

    public function component(string | Closure $component): static
    {
        $this->component = $component;

        return $this;
    }

    public function getComponent(): string
    {
        return $this->evaluate($this->component);
    }

    public function lazy(bool | Closure $condition = true): static
    {
        $this->isLazy = $condition;

        return $this;
    }

    public function isLazy(): bool
    {
        return (bool) $this->evaluate($this->isLazy);
    }

    /**
     * @param  array<string, mixed> | Closure  $data
     */
    public function data(array | Closure $data): static
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function getData(): array
    {
        return $this->evaluate($this->data);
    }

    /**
     * @return array<string, mixed>
     */
    public function getComponentProperties(): array
    {
        $properties = [
            'record' => $this->getRecord(),
        ];

        if ($this->isLazy()) {
            $properties['lazy'] = true;
        }

        return [
            ...$properties,
            ...$this->getData(),
        ];
    }

    public function getId(): ?string
    {
        return $this->getCustomId();
    }

    public function toEmbeddedHtml(): string
    {
        $extraAttributes = $this->getExtraAttributes();
        $id = $this->getId();
        $hasWrapper = filled($id) || filled($extraAttributes);

        $component = $this->getComponent();
        $properties = $this->getComponentProperties();
        $key = $this->getLivewireKey() ?? "{$this->getLivewire()->getId()}.{$component}";

        $livewireHtml = LivewireFacade::mount($component, $properties, $key);

        if ($hasWrapper) {
            $attributes = (new FilamentComponentAttributeBag)
                ->merge(['id' => $id], escape: false)
                ->merge($extraAttributes, escape: false);

            return '<div ' . $attributes->toHtml() . '>' . $livewireHtml . '</div>';
        }

        return $livewireHtml;
    }
}
