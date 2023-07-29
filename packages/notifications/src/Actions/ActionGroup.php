<?php

namespace Filament\Notifications\Actions;

use Filament\Actions\ActionGroup as BaseActionGroup;
use Filament\Support\Enums\IconPosition;
use Filament\Support\Enums\IconSize;
use Illuminate\Contracts\Support\Arrayable;

/**
 * @property array<Action> $actions
 */
class ActionGroup extends BaseActionGroup implements Arrayable
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'actions' => collect($this->getActions())->toArray(),
            'color' => $this->getColor(),
            'icon' => $this->getIcon(),
            'iconPosition' => match ($iconPosition = $this->getIconPosition()) {
                IconPosition::After => 'after',
                IconPosition::Before => 'before',
                default => $iconPosition,
            },
            'iconSize' => match ($iconSize = $this->getIconSize()) {
                IconSize::Small => 'sm',
                IconSize::Medium => 'md',
                IconSize::Large => 'large',
                default => $iconSize,
            },
            'label' => $this->getLabel(),
            'tooltip' => $this->getTooltip(),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     */
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
        $static->iconSize($data['iconSize'] ?? null);
        $static->label($data['label'] ?? null);
        $static->tooltip($data['tooltip'] ?? null);

        return $static;
    }
}
