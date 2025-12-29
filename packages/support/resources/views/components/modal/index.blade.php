@php
    use Filament\Support\Enums\Alignment;
    use Filament\Support\Enums\Width;
    use Filament\Support\View\Components\ModalComponent\IconComponent;
    use Illuminate\View\ComponentAttributeBag;
@endphp

@props([
    'alignment' => Alignment::Start,
    'ariaLabelledby' => null,
    'autofocus' => \Filament\Support\View\Components\ModalComponent::$isAutofocused,
    'closeButton' => \Filament\Support\View\Components\ModalComponent::$hasCloseButton,
    'closeByClickingAway' => \Filament\Support\View\Components\ModalComponent::$isClosedByClickingAway,
    'closeByEscaping' => \Filament\Support\View\Components\ModalComponent::$isClosedByEscaping,
    'closeEventName' => 'close-modal',
    'closeQuietlyEventName' => 'close-modal-quietly',
    'description' => null,
    'extraModalWindowAttributeBag' => null,
    'footer' => null,
    'footerActions' => [],
    'footerActionsAlignment' => Alignment::Start,
    'header' => null,
    'heading' => null,
    'icon' => null,
    'iconAlias' => null,
    'iconColor' => 'primary',
    'id' => null,
    'openEventName' => 'open-modal',
    'slideOver' => false,
    'stickyFooter' => false,
    'stickyHeader' => false,
    'teleport' => null,
    'trigger' => null,
    'visible' => true,
    'width' => 'sm',
])

@php
    $hasContent = ! \Filament\Support\is_slot_empty($slot);
    $hasDescription = filled($description);
    $hasFooter = (! \Filament\Support\is_slot_empty($footer)) || (is_array($footerActions) && count($footerActions)) || (! is_array($footerActions) && (! \Filament\Support\is_slot_empty($footerActions)));
    $hasHeading = filled($heading);
    $hasIcon = filled($icon);

    if (! $alignment instanceof Alignment) {
        $alignment = filled($alignment) ? (Alignment::tryFrom($alignment) ?? $alignment) : null;
    }

    if (! $footerActionsAlignment instanceof Alignment) {
        $footerActionsAlignment = filled($footerActionsAlignment) ? (Alignment::tryFrom($footerActionsAlignment) ?? $footerActionsAlignment) : null;
    }

    if (is_string($width)) {
        $width = Width::tryFrom($width) ?? $width;
    }

    $closeEventHandler = filled($id) ? '$dispatch(' . \Illuminate\Support\Js::from($closeEventName) . ', { id: ' . \Illuminate\Support\Js::from($id) . ' })' : 'close()';

    $wireSubmitHandler = $attributes->get('wire:submit.prevent');
    $attributes = $attributes->except(['wire:submit.prevent']);
@endphp

@if ($trigger)
    {!! '<div>' !!}
    {{-- Avoid formatting issues with unclosed elements --}}

    <div
        @if (! $trigger->attributes->get('disabled'))
            @if ($id)
                x-on:click="$dispatch(@js($openEventName), { id: @js($id) })"
            @else
                x-on:click="$el.nextElementSibling.dispatchEvent(new CustomEvent(@js($openEventName)))"
            @endif
        @endif
        {{ $trigger->attributes->except(['disabled'])->class(['fi-modal-trigger']) }}
    >
        {{ $trigger }}
    </div>
@endif

@if (filled($teleport))
    {!! "<template x-teleport=\"{$teleport}\">" !!}
    {{-- Avoid formatting issues with unclosed elements --}}
@endif

<div
    @if ($ariaLabelledby)
        aria-labelledby="{{ $ariaLabelledby }}"
    @elseif ($heading)
        aria-labelledby="{{ "{$id}.heading" }}"
    @endif
    aria-modal="true"
    id="{{ $id }}"
    role="dialog"
    x-data="filamentModal({
                id: @js($id),
            })"
    @if ($id)
        data-fi-modal-id="{{ $id }}"
        x-on:{{ $closeEventName }}.window="if (($event.detail.id === @js($id)) && isOpen) close()"
        x-on:{{ $closeQuietlyEventName }}.window="if (($event.detail.id === @js($id)) && isOpen) closeQuietly()"
        x-on:{{ $openEventName }}.window="if (($event.detail.id === @js($id)) && (! isOpen)) open()"
    @else
        x-on:{{ $closeEventName }}.stop="if (isOpen) close()"
        x-on:{{ $closeQuietlyEventName }}.stop="if (isOpen) closeQuietly()"
        x-on:{{ $openEventName }}.stop="if (! isOpen) open()"
    @endif
    x-bind:class="{
        'fi-modal-open': isOpen,
    }"
    x-cloak
    x-show="isOpen"
    x-trap.noscroll{{ $autofocus ? '' : '.noautofocus' }}="isOpen"
    {{
        $attributes->class([
            'fi-modal',
            'fi-absolute-positioning-context',
            'fi-modal-slide-over' => $slideOver,
            'fi-modal-has-sticky-header' => $stickyHeader,
            'fi-modal-has-sticky-footer' => $stickyFooter,
            'fi-width-screen' => $width === Width::Screen,
        ])
    }}
>
    <div
        aria-hidden="true"
        x-show="isOpen"
        x-transition.duration.300ms.opacity
        class="fi-modal-close-overlay"
    ></div>

    <div
        @if ($closeByClickingAway)
            x-on:click.self="{{ $closeEventHandler }}"
        @endif
        @class([
            'fi-modal-window-ctn',
            'fi-clickable' => $closeByClickingAway,
        ])
    >
        <{{ filled($wireSubmitHandler) ? 'form' : 'div' }}
            @if ($closeByEscaping)
                x-on:keydown.window.escape="{{ $closeEventHandler }}"
            @endif
            x-show="isWindowVisible"
            x-transition:enter="fi-transition-enter"
            x-transition:leave="fi-transition-leave"
            @if ($width !== Width::Screen)
                x-transition:enter-start="fi-transition-enter-start"
                x-transition:enter-end="fi-transition-enter-end"
                x-transition:leave-start="fi-transition-leave-start"
                x-transition:leave-end="fi-transition-leave-end"
            @endif
            @if (filled($wireSubmitHandler))
                wire:submit.prevent="{!! $wireSubmitHandler !!}"
            @endif
            @if (filled($id))
                wire:key="{{ isset($this) ? "{$this->getId()}." : '' }}modal.{{ $id }}.window"
            @endif
            {{
                ($extraModalWindowAttributeBag ?? new \Illuminate\View\ComponentAttributeBag)->class([
                    'fi-modal-window',
                    'fi-modal-window-has-close-btn' => $closeButton,
                    'fi-modal-window-has-content' => $hasContent,
                    'fi-modal-window-has-footer' => $hasFooter,
                    'fi-modal-window-has-icon' => $hasIcon,
                    'fi-hidden' => ! $visible,
                    ($alignment instanceof Alignment) ? "fi-align-{$alignment->value}" : null,
                    ($width instanceof Width) ? "fi-width-{$width->value}" : (is_string($width) ? $width : null),
                ])
            }}
        >
            @if ($heading || $header)
                <div
                    @if (filled($id))
                        wire:key="{{ isset($this) ? "{$this->getId()}." : '' }}modal.{{ $id }}.header"
                    @endif
                    @class([
                        'fi-modal-header',
                        'fi-vertical-align-center' => $hasIcon && $hasHeading && (! $hasDescription) && in_array($alignment, [Alignment::Start, Alignment::Left]),
                    ])
                >
                    @if ($closeButton)
                        <x-filament::icon-button
                            color="gray"
                            :icon="\Filament\Support\Icons\Heroicon::OutlinedXMark"
                            :icon-alias="\Filament\Support\View\SupportIconAlias::MODAL_CLOSE_BUTTON"
                            icon-size="lg"
                            :label="__('filament::components/modal.actions.close.label')"
                            tabindex="-1"
                            :x-on:click="$closeEventHandler"
                            class="fi-modal-close-btn"
                        />
                    @endif

                    @if ($header)
                        {{ $header }}
                    @else
                        @if ($hasIcon)
                            <div class="fi-modal-icon-ctn">
                                <div
                                    {{ (new ComponentAttributeBag)->color(IconComponent::class, $iconColor)->class(['fi-modal-icon-bg']) }}
                                >
                                    {{ \Filament\Support\generate_icon_html($icon, $iconAlias, size: \Filament\Support\Enums\IconSize::Large) }}
                                </div>
                            </div>
                        @endif

                        <div>
                            <h2 class="fi-modal-heading">
                                {{ $heading }}
                            </h2>

                            @if ($hasDescription)
                                <p class="fi-modal-description">
                                    {{ $description }}
                                </p>
                            @endif
                        </div>
                    @endif
                </div>
            @endif

            @if ($hasContent)
                <div
                    @if (filled($id))
                        wire:key="{{ isset($this) ? "{$this->getId()}." : '' }}modal.{{ $id }}.content"
                    @endif
                    class="fi-modal-content"
                >
                    {{ $slot }}
                </div>
            @endif

            @if ($hasFooter)
                <div
                    @if (filled($id))
                        wire:key="{{ isset($this) ? "{$this->getId()}." : '' }}modal.{{ $id }}.footer"
                    @endif
                    @class([
                        'fi-modal-footer',
                        ($footerActionsAlignment instanceof Alignment) ? "fi-align-{$footerActionsAlignment->value}" : null,
                    ])
                >
                    @if (! \Filament\Support\is_slot_empty($footer))
                        {{ $footer }}
                    @else
                        <div class="fi-modal-footer-actions">
                            @if (is_array($footerActions))
                                @foreach ($footerActions as $action)
                                    {{ $action }}
                                @endforeach
                            @else
                                {{ $footerActions }}
                            @endif
                        </div>
                    @endif
                </div>
            @endif
        </{{ filled($wireSubmitHandler) ? 'form' : 'div' }}>
    </div>
</div>

@if (filled($teleport))
    {!! '</template>' !!}
    {{-- Avoid formatting issues with unclosed elements --}}
@endif

@if ($trigger)
    {!! '</div>' !!}
    {{-- Avoid formatting issues with unclosed elements --}}
@endif
