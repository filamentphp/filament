<?php

namespace Filament\Notifications;

use Filament\Notifications\Actions\Action;
use Filament\Notifications\Concerns\HasActions;
use Filament\Notifications\Concerns\HasDescription;
use Filament\Notifications\Concerns\HasDuration;
use Filament\Notifications\Concerns\HasIcon;
use Filament\Notifications\Concerns\HasId;
use Filament\Notifications\Concerns\HasTitle;
use Filament\Support\Components\ViewComponent;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Str;

class Notification extends ViewComponent implements Arrayable
{
    use HasActions;
    use HasDescription;
    use HasDuration;
    use HasIcon;
    use HasId;
    use HasTitle;

    protected string $view = 'notifications::notification';

    public function __construct(string $id)
    {
        $this->id($id);
    }

    public static function make(?string $id = null): static
    {
        $static = app(static::class, ['id' => $id ?? Str::orderedUuid()]);
        $static->configure();

        return $static;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'title' => $this->getTitle(),
            'description' => $this->getDescription(),
            'icon' => $this->getIcon(),
            'iconColor' => $this->getIconColor(),
            'actions' => collect($this->getActions())->toArray(),
            'duration' => $this->getDuration(),
        ];
    }

    public static function fromArray($value): static
    {
        $static = static::make($value['id']);

        foreach ($value as $key => $value) {
            match ($key) {
                'id' => null,
                'actions' => $static->actions = array_map(fn (array $action): Action => Action::fromArray($action), $value),
                default => $static->{$key} = $value,
            };
        }

        return $static;
    }

    public function send(): static
    {
        session()->push(
            'filament.notifications',
            $this->toArray(),
        );

        return $this;
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
