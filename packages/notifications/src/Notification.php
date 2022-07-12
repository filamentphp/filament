<?php

namespace Filament\Notifications;

use Filament\Notifications\Actions\Action;
use Filament\Support\Components\ViewComponent;
use Livewire\Wireable;
use Illuminate\Support\Str;

class Notification extends ViewComponent implements Wireable
{
    protected string $view = 'notifications::components.notification';

    protected string $id;

    protected ?string $title = null;

    protected ?string $description = null;

    protected ?string $icon = null;

    protected string $iconColor = 'secondary';

    protected array $actions = [];

    public function __construct()
    {
        $this->id(Str::uuid());
    }

    public static function make(): static
    {
        $static = app(static::class);
        $static->setUp();

        return $static;
    }

    protected function setUp(): void
    {
    }

    public function toLivewire(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'icon' => $this->icon,
            'iconColor' => $this->iconColor,
            'actions' => array_map(fn (Action $action): array => $action->toLivewire(), $this->actions),
        ];
    }

    public static function fromLivewire($value): static
    {
        $static = static::make();

        foreach ($value as $key => $value) {
            if ($key === 'actions') {
                $static->{$key} = array_map(fn (array $action): Action => Action::fromLivewire($action), $value);

                continue;
            }

            $static->{$key} = $value;
        }

        return $static;
    }

    public function id(string $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function title(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function description(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function icon(?string $icon): static
    {
        $this->icon = $icon;

        return $this;
    }

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function iconColor(string $color): static
    {
        $this->iconColor = $color;

        return $this;
    }

    public function getIconColor(): ?string
    {
        return $this->iconColor;
    }

    public function status(string $status): static
    {
        return match ($status) {
            'success' => $this->success(),
            'warning' => $this->warning(),
            'danger' => $this->danger(),
            default => $this,
        };
    }

    public function success(): static
    {
        $this->icon('heroicon-o-check-circle');
        $this->iconColor('success');

        return $this;
    }

    public function warning(): static
    {
        $this->icon('heroicon-o-exclamation-circle');
        $this->iconColor('warning');

        return $this;
    }

    public function danger(): static
    {
        $this->icon('heroicon-o-x-circle');
        $this->iconColor('danger');

        return $this;
    }

    public function actions(array $actions): static
    {
        $this->actions = $actions;

        return $this;
    }

    public function getActions(): array
    {
        return $this->actions;
    }
}
