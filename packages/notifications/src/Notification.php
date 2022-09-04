<?php

namespace Filament\Notifications;

use Filament\Notifications\Actions\Action;
use Filament\Notifications\Actions\ActionGroup;
use Filament\Notifications\Concerns\HasActions;
use Filament\Notifications\Concerns\HasBody;
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
    use HasBody;
    use HasDuration;
    use HasIcon;
    use HasId;
    use HasTitle;

    protected string $view = 'notifications::notification';

    protected string $viewIdentifier = 'notification';

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
            'actions' => array_map(fn (Action | ActionGroup $action): array => $action->toArray(), $this->getActions()),
            'body' => $this->getBody(),
            'duration' => $this->getDuration(),
            'icon' => $this->getIcon(),
            'iconColor' => $this->getIconColor(),
            'title' => $this->getTitle(),
        ];
    }

    public static function fromArray(array $data): static
    {
        $static = static::make($data['id']);
        $static->actions(
            array_map(
                fn (array $action): Action | ActionGroup => match (array_key_exists('actions', $action)) {
                    true => ActionGroup::fromArray($action),
                    false => Action::fromArray($action),
                },
                $data['actions'],
            ),
        );
        $static->body($data['body']);
        $static->duration($data['duration']);
        $static->icon($data['icon']);
        $static->iconColor($data['iconColor']);
        $static->title($data['title']);

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
