@php
    use Filament\Support\Enums\Alignment;
    use Filament\Support\Enums\MaxWidth;
    use Filament\Support\Facades\FilamentView;
@endphp

@props([
    'alignment' => Alignment::Start,
    'ariaLabelledby' => null,
    'autofocus' => \Filament\Support\View\Components\Modal::$isAutofocused,
    'closeButton' => \Filament\Support\View\Components\Modal::$hasCloseButton,
    'closeByClickingAway' => \Filament\Support\View\Components\Modal::$isClosedByClickingAway,
    'closeByEscaping' => \Filament\Support\View\Components\Modal::$isClosedByEscaping,
    'closeEventName' => 'close-modal',
    'description' => null,
    'displayClasses' => 'inline-block',
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
    'trigger' => null,
    'visible' => true,
    'width' => 'sm',
])

@php
    $hasDescription = filled($description);
    $hasHeading = filled($heading);
    $hasIcon = filled($icon);

    if (! $alignment instanceof Alignment) {
        $alignment = filled($alignment) ? (Alignment::tryFrom($alignment) ?? $alignment) : null;
    }

    if (! $footerActionsAlignment instanceof Alignment) {
        $footerActionsAlignment = filled($footerActionsAlignment) ? (Alignment::tryFrom($footerActionsAlignment) ?? $footerActionsAlignment) : null;
    }

    if (! $width instanceof MaxWidth) {
        $width = filled($width) ? (MaxWidth::tryFrom($width) ?? $width) : null;
    }

    $closeEventHandler = filled($id) ? '$dispatch(' . \Illuminate\Support\Js::from($closeEventName) . ', { id: ' . \Illuminate\Support\Js::from($id) . ' })' : 'close()';
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
            this.$nextTick(() => {
                this.isOpen = true

                @if (FilamentView::hasSpaMode())
                    this.$dispatch('ax-modal-opened')
                @endif

                this.$refs.modalContainer.dispatchEvent(
                    new CustomEvent('modal-opened', { id: '{{ $id }}' }),
                )
            })
        },
    }"
    @if ($id)
        x-on:{{ $closeEventName }}.window="if ($event.detail.id === '{{ $id }}') close()"
        x-on:{{ $openEventName }}.window="if ($event.detail.id === '{{ $id }}') open()"
    @endif
    x-trap.noscroll{{ $autofocus ? '' : '.noautofocus' }}="isOpen"
    x-bind:class="{
        'fi-modal-open': isOpen,
    }"
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

    <div x-cloak x-show="isOpen">
        <div
            aria-hidden="true"
            x-show="isOpen"
            x-transition.duration.300ms.opacity
            @class([
                'fi-modal-close-overlay fixed inset-0 z-40 bg-gray-950/50 dark:bg-gray-950/75',
            ])
        ></div>

        <div
            @class([
                'fixed inset-0 z-40',
                'overflow-y-auto' => ! ($slideOver || ($width === MaxWidth::Screen)),
                'cursor-pointer' => $closeByClickingAway,
            ])
        >
            <div
                x-ref="modalContainer"
                @if ($closeByClickingAway)
                    {{-- Ensure that the click element is not triggered from a user selecting text inside an input. --}}
                    x-on:click.self="
                        document.activeElement.selectionStart === undefined &&
                            document.activeElement.selectionEnd === undefined &&
                            {{ $closeEventHandler }}
                    "
                @endif
                {{
                    $attributes->class([
                        'relative grid min-h-full grid-rows-[1fr_auto_1fr] justify-items-center sm:grid-rows-[1fr_auto_3fr]',
                        'p-4' => ! ($slideOver || ($width === MaxWidth::Screen)),
                    ])
                }}
            >
                <div
                    x-data="{ isShown: false }"
                    x-init="
                        $nextTick(() => {
                            isShown = isOpen
                            $watch('isOpen', () => (isShown = isOpen))
                        })
                    "
                    @if ($closeByEscaping)
                        x-on:keydown.window.escape="{{ $closeEventHandler }}"
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
                        x-transition:enter-start="scale-95 opacity-0"
                        x-transition:enter-end="scale-100 opacity-100"
                        x-transition:leave-start="scale-100 opacity-100"
                        x-transition:leave-end="scale-95 opacity-0"
                    @endif
                    {{
                        ($extraModalWindowAttributeBag ?? new \Illuminate\View\ComponentAttributeBag)->class([
                            'fi-modal-window pointer-events-auto relative row-start-2 flex w-full cursor-default flex-col bg-white shadow-xl ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10',
                            'fi-modal-slide-over-window ms-auto overflow-y-auto' => $slideOver,
                            // Using an arbitrary value instead of the h-dvh class that was added in Tailwind CSS v3.4.0
                            // to ensure compatibility with custom themes that may use an older version of Tailwind CSS.
                            'h-[100dvh]' => $slideOver || ($width === MaxWidth::Screen),
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
                    }}
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
                                'items-center' => $hasIcon && $hasHeading && (! $hasDescription) && in_array($alignment, [Alignment::Start, Alignment::Left]),
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
                                        :x-on:click="$closeEventHandler"
                                        class="fi-modal-close-btn"
                                    />
                                </div>
                            @endif

                            @if ($header)
                                {{ $header }}
                            @else
                                @if ($hasIcon)
                                    <div
                                        @class([
                                            'mb-5 flex items-center justify-center' => $alignment === Alignment::Center,
                                        ])
                                    >
                                        <div
                                            @class([
                                                'rounded-full',
                                                match ($iconColor) {
                                                    'gray' => 'bg-gray-100 dark:bg-gray-500/20',
                                                    default => 'fi-color-custom bg-custom-100 dark:bg-custom-500/20',
                                                },
                                                is_string($iconColor) ? "fi-color-{$iconColor}" : null,
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

                                    @if ($hasDescription)
                                        <x-filament::modal.description
                                            class="mt-2"
                                        >
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
                                'pe-6 ps-[5.25rem]' => $hasIcon && ($alignment === Alignment::Start) && (! $stickyHeader),
                                'px-6' => ! ($hasIcon && ($alignment === Alignment::Start) && (! $stickyHeader)),
                            ])
                        >
                            {{ $slot }}
                        </div>
                    @endif

                    @if ((! \Filament\Support\is_slot_empty($footer)) || (is_array($footerActions) && count($footerActions)) || (! is_array($footerActions) && (! \Filament\Support\is_slot_empty($footerActions))))
                        <div
                            @class([
                                'fi-modal-footer w-full',
                                'pe-6 ps-[5.25rem]' => $hasIcon && ($alignment === Alignment::Start) && ($footerActionsAlignment !== Alignment::Center) && (! $stickyFooter),
                                'px-6' => ! ($hasIcon && ($alignment === Alignment::Start) && ($footerActionsAlignment !== Alignment::Center) && (! $stickyFooter)),
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
</div>
