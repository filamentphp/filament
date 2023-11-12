<?php

namespace Filament\Notifications\Actions;

use Filament\Actions\ActionGroup as BaseActionGroup;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Str;

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
            'dropdownMaxHeight' => $this->getDropdownMaxHeight(),
            'dropdownPlacement' => $this->getDropdownPlacement(),
            'dropdownWidth' => $this->getDropdownWidth(),
            'extraAttributes' => $this->getExtraAttributes(),
            'hasDropdown' => $this->hasDropdown(),
            'icon' => $this->getIcon(),
            'iconPosition' => $this->getIconPosition(),
            'iconSize' => $this->getIconSize(),
            'isOutlined' => $this->isOutlined(),
            'label' => $this->getLabel(),
            'size' => $this->getSize(),
            'tooltip' => $this->getTooltip(),
            'view' => $this->getView(),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): static
    {
        $static = static::make(
            array_map(
                fn (array $action): Action | ActionGroup => match (array_key_exists('actions', $action)) {
                    true => ActionGroup::fromArray($action),
                    false => Action::fromArray($action),
                },
                $data['actions'] ?? [],
            ),
        );

        $view = $data['view'] ?? null;

        if (filled($view) && ($static->getView() !== $view) && static::isViewSafe($view)) {
            $static->view($view);
        }

        if (filled($size = $data['size'] ?? null)) {
            $static->size($size);
        }

        $static->color($data['color'] ?? null);
        $static->dropdown($data['hasDropdown'] ?? false);
        $static->dropdownMaxHeight($data['dropdownMaxHeight'] ?? null);
        $static->dropdownPlacement($data['dropdownPlacement'] ?? null);
        $static->dropdownWidth($data['dropdownWidth'] ?? null);
        $static->extraAttributes($data['extraAttributes'] ?? []);
        $static->icon($data['icon'] ?? null);
        $static->iconPosition($data['iconPosition'] ?? null);
        $static->iconSize($data['iconSize'] ?? null);
        $static->label($data['label'] ?? null);
        $static->outlined($data['isOutlined'] ?? null);
        $static->tooltip($data['tooltip'] ?? null);

        return $static;
    }

    /**
     * @param  view-string  $view
     */
    protected static function isViewSafe(string $view): bool
    {
        return Str::startsWith($view, 'filament-actions::');
    }
}
