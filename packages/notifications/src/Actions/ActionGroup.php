<?php

namespace Filament\Notifications\Actions;

use Filament\Support\Actions\ActionGroup as BaseActionGroup;
use Illuminate\Contracts\Support\Arrayable;

class ActionGroup extends BaseActionGroup implements Arrayable
{
    protected string $view = 'notifications::actions.group';

    public function toArray(): array
    {
        return [
            'actions' => collect($this->getActions())->toArray(),
            'color' => $this->getColor(),
            'icon' => $this->getIcon(),
            'iconPosition' => $this->getIconPosition(),
            'label' => $this->getLabel(),
            'tooltip' => $this->getTooltip(),
        ];
    }

    public static function fromArray(array $data): static
    {
        $static = static::make(
            array_map(
                fn (array $action): Action => Action::fromArray($action),
                $data['actions'] ?? [],
            ),
        );

        $static->color($data['color'] ?? null);
        $static->icon($data['icon'] ?? null);
        $static->iconPosition($data['iconPosition'] ?? null);
        $static->label($data['label'] ?? null);
        $static->tooltip($data['tooltip'] ?? null);

        return $static;
    }
}
