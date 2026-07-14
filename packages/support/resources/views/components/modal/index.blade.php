@php
    use Filament\Support\Enums\Alignment;
    use Filament\Support\Enums\SlideOverPosition;
    use Filament\Support\Enums\Width;
    use Filament\Support\View\ComponentAttributeBag as FilamentComponentAttributeBag;
    use Filament\Support\View\Components\ModalComponent\IconComponent;
    use Illuminate\Contracts\Support\Htmlable;
@endphp

@props([
    'alert' => false,
    'alignment' => Alignment::Start,
    'ariaLabelledby' => null,
    'autofocus' => \Filament\Support\View\Components\ModalComponent::$isAutofocused,
    'clickThrough' => false,
    'closeButton' => \Filament\Support\View\Components\ModalComponent::$hasCloseButton,
    'closeByClickingAway' => \Filament\Support\View\Components\ModalComponent::$isClosedByClickingAway,
    'closeByEscaping' => \Filament\Support\View\Components\ModalComponent::$isClosedByEscaping,
    'closeEventName' => 'close-modal',
    'closeQuietlyEventName' => 'close-modal-quietly',
    'description' => null,
    'focusTrapReturnsFocus' => true,
    'extraModalWindowAttributeBag' => null,
    'extraModalOverlayAttributeBag' => null,
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
    'slideOverPosition' => SlideOverPosition::End,
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
    $iconHtml = ($icon || $iconAlias) ? \Filament\Support\generate_icon_html($icon, $iconAlias, size: \Filament\Support\Enums\IconSize::Large) : null;
    $hasIcon = $iconHtml !== null;

    $headingId = filled($id) ? "{$id}.heading" : null;

    // The description is only rendered when the built-in heading is, so the
    // `aria-describedby` reference must be gated to the same conditions.
    $descriptionId = ($hasDescription && $hasHeading && (! $header) && filled($id)) ? "{$id}.description" : null;

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

    $isClickThrough = (bool) $clickThrough;

    // Click-through and closing by clicking away are incompatible, so enabling
    // click-through silently disables closing the modal by clicking away.
    if ($isClickThrough) {
        $closeByClickingAway = false;
    }
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
    @if ($descriptionId)
        aria-describedby="{{ $descriptionId }}"
    @endif
    @if ($ariaLabelledby)
        aria-labelledby="{{ $ariaLabelledby }}"
    @elseif ($hasHeading && $headingId)
        aria-labelledby="{{ $headingId }}"
    @elseif ($hasHeading)
        aria-label="{{ trim(strip_tags($heading instanceof Htmlable ? $heading->toHtml() : $heading)) }}"
    @endif
    aria-modal="{{ $isClickThrough ? 'false' : 'true' }}"
    id="{{ $id }}"
    role="{{ $alert ? 'alertdialog' : 'dialog' }}"
    tabindex="-1"
    x-data="filamentModal({
                id: @js($id),
                isScrollLocked: @js(! $isClickThrough),
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
    @if (! $isClickThrough)
        x-trap{{ $focusTrapReturnsFocus ? '' : '.noreturn' }}{{ $autofocus ? '' : '.noautofocus' }}="isTrapActive"
    @endif
    {{
        $attributes->class([
            'fi-modal',
            'fi-absolute-positioning-context',
            'fi-modal-slide-over' => $slideOver,
            'fi-modal-slide-over-from-start' => $slideOver && $slideOverPosition === SlideOverPosition::Start,
            'fi-modal-slide-over-from-end' => $slideOver && $slideOverPosition === SlideOverPosition::End,
            'fi-modal-has-sticky-header' => $stickyHeader,
            'fi-modal-has-sticky-footer' => $stickyFooter,
            'fi-width-screen' => $width === Width::Screen,
            'fi-modal-click-through' => $isClickThrough,
        ])
    }}
>
    @if (! $isClickThrough)
        <div
            aria-hidden="true"
            x-show="isOpen"
            x-transition.duration.300ms.opacity
            {{
                ($extraModalOverlayAttributeBag ?? new \Filament\Support\View\ComponentAttributeBag)->class([
                    'fi-modal-close-overlay',
                ])
            }}
        ></div>
    @endif

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
                x-on:keydown.window.escape="if (isTopmost()) {{ $closeEventHandler }}"
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
                ($extraModalWindowAttributeBag ?? new \Filament\Support\View\ComponentAttributeBag)->merge([
                    // When `Escape` does not close the modal, the close button stays in the tab order as the only keyboard way to dismiss it, so the window takes the focus trap's `[autofocus]` to stop the button from being autofocused when the modal opens.
                    'autofocus' => $closeButton && (! $closeByEscaping) && ($heading || $header),
                    'tabindex' => ($closeButton && (! $closeByEscaping) && ($heading || $header)) ? '-1' : null,
                ])->class([
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
                        {{-- The close button is removed from the tab order when `Escape` also closes the modal, so it can sit first in the focus trap without being autofocused when the modal opens. When `Escape` does not close the modal, the button is the only keyboard way to dismiss it, so it stays in the tab order and the modal window is autofocused instead. --}}
                        <x-filament::icon-button
                            color="gray"
                            :icon="\Filament\Support\Icons\Heroicon::OutlinedXMark"
                            :icon-alias="\Filament\Support\View\SupportIconAlias::MODAL_CLOSE_BUTTON"
                            icon-size="lg"
                            :label="__('filament::components/modal.actions.close.label')"
                            :tabindex="$closeByEscaping ? '-1' : null"
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
                                    {{ (new FilamentComponentAttributeBag)->color(IconComponent::class, $iconColor)->class(['fi-modal-icon-bg']) }}
                                >
                                    {{ $iconHtml }}
                                </div>
                            </div>
                        @endif

                        <div>
                            <h2
                                @if ($headingId)
                                    id="{{ $headingId }}"
                                @endif
                                class="fi-modal-heading"
                            >
                                {{ $heading }}
                            </h2>

                            @if ($hasDescription)
                                <p
                                    @if ($descriptionId)
                                        id="{{ $descriptionId }}"
                                    @endif
                                    class="fi-modal-description"
                                >
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
