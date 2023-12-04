@php
    use Filament\Support\Enums\Alignment;
    use Filament\Support\Enums\MaxWidth;
@endphp

@props([
    'alignment' => Alignment::Start,
    'ariaLabelledby' => null,
    'closeButton' => \Filament\Support\View\Components\Modal::$hasCloseButton,
    'closeByClickingAway' => \Filament\Support\View\Components\Modal::$isClosedByClickingAway,
    'closeEventName' => 'close-modal',
    'description' => null,
    'displayClasses' => 'inline-block',
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
    'trigger' => null,
    'visible' => true,
    'width' => 'sm',
])

@php
    if (! $alignment instanceof Alignment) {
        $alignment = filled($alignment) ? (Alignment::tryFrom($alignment) ?? $alignment) : null;
    }

    if (! $footerActionsAlignment instanceof Alignment) {
        $footerActionsAlignment = filled($footerActionsAlignment) ? (Alignment::tryFrom($footerActionsAlignment) ?? $footerActionsAlignment) : null;
    }

    if (! $width instanceof MaxWidth) {
        $width = filled($width) ? (MaxWidth::tryFrom($width) ?? $width) : null;
    }
@endphp

<div
    @if ($ariaLabelledby)
        aria-labelledby="{{ $ariaLabelledby }}"
    @elseif ($heading)
        aria-labelledby="{{ "{$id}.heading" }}"
    @endif
    aria-modal="true"
    role="dialog"
    x-data="{
        isOpen: false,

        livewire: null,

        close: function () {
            this.isOpen = false

            this.$refs.modalContainer.dispatchEvent(
                new CustomEvent('modal-closed', { id: '{{ $id }}' }),
            )
        },

        open: function () {
            this.isOpen = true

            this.$refs.modalContainer.dispatchEvent(
                new CustomEvent('modal-opened', { id: '{{ $id }}' }),
            )
        },
    }"
    @if ($id)
        x-on:{{ $closeEventName }}.window="if ($event.detail.id === '{{ $id }}') close()"
        x-on:{{ $openEventName }}.window="if ($event.detail.id === '{{ $id }}') open()"
    @endif
    x-trap.noscroll="isOpen"
    wire:ignore.self
    @class([
        'fi-modal',
        'fi-width-screen' => $width === MaxWidth::Screen,
        $displayClasses,
    ])
>
    @if ($trigger)
        <div
            x-on:click="open"
            {{ $trigger->attributes->class(['fi-modal-trigger flex cursor-pointer']) }}
        >
            {{ $trigger }}
        </div>
    @endif

    <div
        x-cloak
        x-show="isOpen"
        x-transition.duration.300ms.opacity
        @class([
            'fixed inset-0 z-40 min-h-full overflow-y-auto overflow-x-hidden transition',
            'flex items-center' => ! $slideOver,
        ])
    >
        <div
            aria-hidden="true"
            @if ($closeByClickingAway)
                @if (filled($id))
                    x-on:click="$dispatch('{{ $closeEventName }}', { id: '{{ $id }}' })"
                @else
                    x-on:click="close()"
                @endif
            @endif
            @class([
                'fi-modal-close-overlay fixed inset-0 bg-gray-950/50 dark:bg-gray-950/75',
                'cursor-pointer' => $closeByClickingAway,
            ])
            style="will-change: transform"
        ></div>

        <div
            x-cloak
            x-ref="modalContainer"
            {{
                $attributes->class([
                    'pointer-events-none relative w-full transition',
                    'my-auto p-4' => ! ($slideOver || ($width === MaxWidth::Screen)),
                ])
            }}
        >
            <div
                x-cloak
                x-data="{ isShown: false }"
                x-init="
                    $nextTick(() => {
                        isShown = isOpen
                        $watch('isOpen', () => (isShown = isOpen))
                    })
                "
                @if (filled($id))
                    x-on:keydown.window.escape="$dispatch('{{ $closeEventName }}', { id: '{{ $id }}' })"
                @else
                    x-on:keydown.window.escape="close()"
                @endif
                x-show="isShown"
                x-transition:enter="duration-300"
                x-transition:leave="duration-300"
                @if ($width === MaxWidth::Screen)
                @elseif ($slideOver)
                    x-transition:enter-start="translate-x-full rtl:-translate-x-full"
                    x-transition:enter-end="translate-x-0"
                    x-transition:leave-start="translate-x-0"
                    x-transition:leave-end="translate-x-full rtl:-translate-x-full"
                @else
                    x-transition:enter-start="scale-95"
                    x-transition:enter-end="scale-100"
                    x-transition:leave-start="scale-95"
                    x-transition:leave-end="scale-100"
                @endif
                @class([
                    'fi-modal-window pointer-events-auto relative flex w-full cursor-default flex-col bg-white shadow-xl ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10',
                    'fi-modal-slide-over-window ms-auto overflow-y-auto' => $slideOver,
                    'h-screen' => $slideOver || ($width === MaxWidth::Screen),
                    'mx-auto rounded-xl' => ! ($slideOver || ($width === MaxWidth::Screen)),
                    'hidden' => ! $visible,
                    match ($width) {
                        MaxWidth::ExtraSmall => 'max-w-xs',
                        MaxWidth::Small => 'max-w-sm',
                        MaxWidth::Medium => 'max-w-md',
                        MaxWidth::Large => 'max-w-lg',
                        MaxWidth::ExtraLarge => 'max-w-xl',
                        MaxWidth::TwoExtraLarge => 'max-w-2xl',
                        MaxWidth::ThreeExtraLarge => 'max-w-3xl',
                        MaxWidth::FourExtraLarge => 'max-w-4xl',
                        MaxWidth::FiveExtraLarge => 'max-w-5xl',
                        MaxWidth::SixExtraLarge => 'max-w-6xl',
                        MaxWidth::SevenExtraLarge => 'max-w-7xl',
                        MaxWidth::Full => 'max-w-full',
                        MaxWidth::MinContent => 'max-w-min',
                        MaxWidth::MaxContent => 'max-w-max',
                        MaxWidth::FitContent => 'max-w-fit',
                        MaxWidth::Prose => 'max-w-prose',
                        MaxWidth::ScreenSmall => 'max-w-screen-sm',
                        MaxWidth::ScreenMedium => 'max-w-screen-md',
                        MaxWidth::ScreenLarge => 'max-w-screen-lg',
                        MaxWidth::ScreenExtraLarge => 'max-w-screen-xl',
                        MaxWidth::ScreenTwoExtraLarge => 'max-w-screen-2xl',
                        MaxWidth::Screen => 'fixed inset-0',
                        default => $width,
                    },
                ])
            >
                @if ($heading || $header)
                    <div
                        @class([
                            'fi-modal-header flex px-6 pt-6',
                            'fi-sticky sticky top-0 z-10 border-b border-gray-200 bg-white pb-6 dark:border-white/10 dark:bg-gray-900' => $stickyHeader,
                            'rounded-t-xl' => $stickyHeader && ! ($slideOver || ($width === MaxWidth::Screen)),
                            match ($alignment) {
                                Alignment::Start, Alignment::Left => 'gap-x-5',
                                Alignment::Center => 'flex-col',
                                default => null,
                            },
                        ])
                    >
                        @if ($closeButton)
                            <div
                                @class([
                                    'absolute',
                                    'end-4 top-4' => ! $slideOver,
                                    'end-6 top-6' => $slideOver,
                                ])
                            >
                                <x-filament::icon-button
                                    color="gray"
                                    icon="heroicon-o-x-mark"
                                    icon-alias="modal.close-button"
                                    icon-size="lg"
                                    :label="__('filament::components/modal.actions.close.label')"
                                    tabindex="-1"
                                    :x-on:click="filled($id) ? '$dispatch(' . \Illuminate\Support\Js::from($closeEventName) . ', { id: ' . \Illuminate\Support\Js::from($id) . ' })' : 'close()'"
                                    class="fi-modal-close-btn"
                                />
                            </div>
                        @endif

                        @if ($header)
                            {{ $header }}
                        @else
                            @if ($icon)
                                <div
                                    @class([
                                        'mb-5 flex items-center justify-center' => $alignment === Alignment::Center,
                                    ])
                                >
                                    <div
                                        @class([
                                            'rounded-full',
                                            match ($iconColor) {
                                                'gray' => 'fi-color-gray bg-gray-100 dark:bg-gray-500/20',
                                                default => 'fi-color-custom bg-custom-100 dark:bg-custom-500/20',
                                            },
                                            match ($alignment) {
                                                Alignment::Start, Alignment::Left => 'p-2',
                                                Alignment::Center => 'p-3',
                                                default => null,
                                            },
                                        ])
                                        @style([
                                            \Filament\Support\get_color_css_variables(
                                                $iconColor,
                                                shades: [100, 400, 500, 600],
                                                alias: 'modal.icon',
                                            ) => $iconColor !== 'gray',
                                        ])
                                    >
                                        <x-filament::icon
                                            :alias="$iconAlias"
                                            :icon="$icon"
                                            @class([
                                                'fi-modal-icon h-6 w-6',
                                                match ($iconColor) {
                                                    'gray' => 'text-gray-500 dark:text-gray-400',
                                                    default => 'text-custom-600 dark:text-custom-400',
                                                },
                                            ])
                                        />
                                    </div>
                                </div>
                            @endif

                            <div
                                @class([
                                    'text-center' => $alignment === Alignment::Center,
                                ])
                            >
                                <x-filament::modal.heading>
                                    {{ $heading }}
                                </x-filament::modal.heading>

                                @if (filled($description))
                                    <x-filament::modal.description class="mt-2">
                                        {{ $description }}
                                    </x-filament::modal.description>
                                @endif
                            </div>
                        @endif
                    </div>
                @endif

                @if (! \Filament\Support\is_slot_empty($slot))
                    <div
                        @class([
                            'fi-modal-content flex flex-col gap-y-4 py-6',
                            'flex-1' => ($width === MaxWidth::Screen) || $slideOver,
                            'pe-6 ps-[5.25rem]' => $icon && ($alignment === Alignment::Start),
                            'px-6' => ! ($icon && ($alignment === Alignment::Start)),
                        ])
                    >
                        {{ $slot }}
                    </div>
                @endif

                @if ((! \Filament\Support\is_slot_empty($footer)) || (is_array($footerActions) && count($footerActions)) || (! is_array($footerActions) && (! \Filament\Support\is_slot_empty($footerActions))))
                    <div
                        @class([
                            'fi-modal-footer w-full',
                            'pe-6 ps-[5.25rem]' => $icon && ($alignment === Alignment::Start) && ($footerActionsAlignment !== Alignment::Center) && (! $stickyFooter),
                            'px-6' => ! ($icon && ($alignment === Alignment::Start) && ($footerActionsAlignment !== Alignment::Center) && (! $stickyFooter)),
                            'fi-sticky sticky bottom-0 border-t border-gray-200 bg-white py-5 dark:border-white/10 dark:bg-gray-900' => $stickyFooter,
                            'rounded-b-xl' => $stickyFooter && ! ($slideOver || ($width === MaxWidth::Screen)),
                            'pb-6' => ! $stickyFooter,
                            'mt-6' => (! $stickyFooter) && \Filament\Support\is_slot_empty($slot),
                            'mt-auto' => $slideOver,
                        ])
                    >
                        @if (! \Filament\Support\is_slot_empty($footer))
                            {{ $footer }}
                        @else
                            <div
                                @class([
                                    'fi-modal-footer-actions gap-3',
                                    match ($footerActionsAlignment) {
                                        Alignment::Start, Alignment::Left => 'flex flex-wrap items-center',
                                        Alignment::Center => 'flex flex-col-reverse sm:grid sm:grid-cols-[repeat(auto-fit,minmax(0,1fr))]',
                                        Alignment::End, Alignment::Right => 'flex flex-row-reverse flex-wrap items-center',
                                        default => null,
                                    },
                                ])
                            >
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
            </div>
        </div>
    </div>
</div>
