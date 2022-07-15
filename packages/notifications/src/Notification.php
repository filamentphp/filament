<?php

namespace Filament\Notifications;

use Closure;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Concerns\HasDuration;
use Filament\Notifications\Concerns\HasIcon;
use Filament\Support\Components\ViewComponent;
use Illuminate\Support\Str;
use Livewire\Wireable;

class Notification extends ViewComponent implements Wireable
{
    use HasDuration;
    use HasIcon;

    protected string $view = 'notifications::notification';

    protected string $id;

    protected string | Closure | null $title = null;

    protected string | Closure | null $description = null;

    protected array | Closure $actions = [];

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
        $this->iconColor('secondary');
    }

    public function toLivewire(): array
    {
        return [
            'id' => $this->getId(),
            'title' => $this->getTitle(),
            'description' => $this->getDescription(),
            'icon' => $this->getIcon(),
            'iconColor' => $this->getIconColor(),
            'actions' => array_map(fn (Action $action): array => $action->toLivewire(), $this->getActions()),
            'duration' => $this->getDuration(),
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

    public function title(string | Closure | null $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->evaluate($this->title);
    }

    public function description(string | Closure | null $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->evaluate($this->description);
    }

    public function actions(array | Closure $actions): static
    {
        $this->actions = $actions;

        return $this;
    }

    public function getActions(): array
    {
        return $this->evaluate($this->actions);
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
}
