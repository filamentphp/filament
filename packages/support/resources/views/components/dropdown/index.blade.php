@props([
    'availableHeight' => null,
    'availableWidth' => null,
    'flip' => true,
    'maxHeight' => null,
    'offset' => 8,
    'placement' => null,
    'shift' => false,
    'size' => false,
    'sizePadding' => 16,
    'teleport' => false,
    'trigger' => null,
    'width' => null,
])

@php
    use Filament\Support\Enums\Width;

    $sizeConfig = collect([
        'availableHeight' => $availableHeight,
        'availableWidth' => $availableWidth,
        'padding' => $sizePadding,
    ])->filter()->toJson();

    if (is_string($width)) {
        $width = Width::tryFrom($width) ?? $width;
    }

    // Generate unique IDs for ARIA associations
    $triggerId = 'dropdown-trigger-' . str()->random(8);
    $panelId = 'dropdown-panel-' . str()->random(8);
@endphp

<div
    x-data="filamentDropdown"
    {{ $attributes->class(['fi-dropdown']) }}
>
    <div
        x-on:mousedown="if ($event.button === 0) toggle($event)"
        x-on:keydown.enter="toggle($event)"
        x-on:keydown.space="toggle($event)"
        x-bind:aria-expanded="$refs.panel && ($refs.panel.hasAttribute('data-open') || $refs.panel.style.display !== 'none') ? 'true' : 'false'"
        aria-haspopup="true"
        aria-controls="{{ $panelId }}"
        id="{{ $triggerId }}"
        {{ $trigger->attributes->class(['fi-dropdown-trigger']) }}
    >
        {{ $trigger }}
    </div>

    @if (! \Filament\Support\is_slot_empty($slot))
        <div
            x-cloak
            x-float{{ $placement ? ".placement.{$placement}" : '' }}{{ $size ? '.size' : '' }}{{ $flip ? '.flip' : '' }}{{ $shift ? '.shift' : '' }}{{ $teleport ? '.teleport' : '' }}{{ $offset ? '.offset' : '' }}="{ offset: {{ $offset ?? 0 }}{{ $size ? (', size: ' . $sizeConfig) : '' }} }"
            x-ref="panel"
            x-on:keydown="handleKeydown($event)"
            x-on:click="handleItemClick($event)"
            x-transition:enter-start="fi-opacity-0"
            x-transition:leave-end="fi-opacity-0"
            role="menu"
            aria-labelledby="{{ $triggerId }}"
            id="{{ $panelId }}"
            @if ($attributes->has('wire:key'))
                wire:ignore.self
                wire:key="{{ $attributes->get('wire:key') }}.panel"
            @endif
            @class([
                'fi-dropdown-panel',
                ($width instanceof Width) ? "fi-width-{$width->value}" : (is_string($width) ? $width : ''),
                'fi-scrollable' => $maxHeight || $size,
            ])
            @style([
                "max-height: {$maxHeight}" => $maxHeight,
            ])
        >
            {{ $slot }}
        </div>
    @endif
</div>
