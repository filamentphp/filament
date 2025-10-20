@props([
    'actions' => [],
    'badge' => null,
    'badgeColor' => null,
    'button' => false,
    'buttonGroup' => null,
    'color' => null,
    'dropdownMaxHeight' => null,
    'dropdownOffset' => null,
    'dropdownPlacement' => null,
    'dropdownWidth' => null,
    'group' => null,
    'icon' => null,
    'iconSize' => null,
    'iconButton' => false,
    'label' => null,
    'link' => false,
    'size' => null,
    'tooltip' => null,
    'triggerView' => null,
    'view' => null,
])

@php
    $group ??= \Filament\Actions\ActionGroup::make($actions)
        ->badgeColor($badgeColor)
        ->color($color)
        ->dropdownMaxHeight($dropdownMaxHeight)
        ->dropdownOffset($dropdownOffset)
        ->dropdownPlacement($dropdownPlacement)
        ->dropdownWidth($dropdownWidth)
        ->icon($icon)
        ->iconSize($iconSize)
        ->label($label)
        ->size($size)
        ->tooltip($tooltip)
        ->triggerView($triggerView)
        ->view($view);

    $badge === true
        ? $group->badge()
        : $group->badge($badge);

    if ($button) {
        $group
            ->button()
            ->iconPosition($attributes->get('iconPosition') ?? $attributes->get('icon-position'))
            ->outlined($attributes->get('outlined') ?? false);
    }

    if ($buttonGroup) {
        $group->buttonGroup();
    }

    if ($iconButton) {
        $group->iconButton();
    }

    if ($link) {
        $group->link();
    }
@endphp

{{ $group }}
