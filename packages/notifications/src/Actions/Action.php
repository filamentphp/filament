<?php

namespace Filament\Notifications\Actions;

use Filament\Support\Components\ViewComponent;
use Livewire\Wireable;
use Illuminate\Support\Str;
use Illuminate\View\ComponentAttributeBag;

class Action extends ViewComponent implements Wireable
{
    protected string $view = 'notifications::actions.link-action';

    protected string $viewIdentifier = 'action';

    protected string $name;

    protected ?string $label = null;

    protected string $color = 'primary';

    protected bool $shouldOpenUrlInNewTab = false;

    protected ?string $url = null;

    protected bool $shouldCloseNotification = false;

    protected ?string $event = null;

    protected bool $isOutlined = false;

    protected array $extraAttributes = [];

    public function __construct(string $name)
    {
        $this->name($name);
    }

    public static function make(string $name): static
    {
        $static = app(static::class, ['name' => $name]);
        $static->setUp();

        return $static;
    }

    protected function setUp(): void
    {
    }

    public function toLivewire(): array
    {
        return [
            'view' => $this->view,
            'name' => $this->name,
            'label' => $this->label,
            'color' => $this->color,
            'shouldOpenUrlInNewTab' => $this->shouldOpenUrlInNewTab,
            'url' => $this->url,
            'event' => $this->event,
            'shouldCloseNotification' => $this->shouldCloseNotification,
            'isOutlined' => $this->isOutlined,
            'extraAttributes' => $this->extraAttributes,
        ];
    }

    public static function fromLivewire($value): static
    {
        $static = static::make($value['name']);

        foreach ($value as $key => $value) {
            $static->{$key} = $value;
        }

        return $static;
    }

    public function button(): static
    {
        $this->view('notifications::actions.button-action');

        return $this;
    }

    public function link(): static
    {
        $this->view('tables::actions.link-action');

        return $this;
    }

    public function name(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function label(?string $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function getLabel(): string
    {
        return $this->label ?? (string) Str::of($this->getName())
            ->kebab()
            ->replace(['-', '_'], ' ')
            ->ucfirst();
    }

    public function color(string $color): static
    {
        $this->color = $color;

        return $this;
    }

    public function getColor(): string
    {
        return $this->color;
    }

    public function openUrlInNewTab(bool $condition = true): static
    {
        $this->shouldOpenUrlInNewTab = $condition;

        return $this;
    }

    public function shouldOpenUrlInNewTab(): bool
    {
        return $this->shouldOpenUrlInNewTab;
    }

    public function url(?string $url, bool $shouldOpenInNewTab = false): static
    {
        $this->shouldOpenUrlInNewTab = $shouldOpenInNewTab;
        $this->url = $url;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function event(?string $event): static
    {
        $this->event = $event;

        return $this;
    }

    public function getEvent(): ?string
    {
        return $this->event;
    }

    public function closeNotification(bool $condition = true): static
    {
        $this->shouldCloseNotification = $condition;

        return $this;
    }

    public function shouldCloseNotification(): bool
    {
        return $this->shouldCloseNotification;
    }

    public function extraAttributes(array $attributes): static
    {
        $this->extraAttributes = $attributes;

        return $this;
    }

    public function getExtraAttributes(): array
    {
        return $this->extraAttributes;
    }

    public function outlined(bool $condition = true): static
    {
        $this->isOutlined = $condition;

        return $this;
    }

    public function isOutlined(): bool
    {
        return $this->isOutlined;
    }

    public function getExtraAttributeBag(): ComponentAttributeBag
    {
        return new ComponentAttributeBag($this->getExtraAttributes());
    }
}
