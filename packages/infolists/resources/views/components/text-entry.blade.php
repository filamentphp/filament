<x-dynamic-component :component="$getEntryWrapperView()" :entry="$entry">
    @php
        $isListWithLineBreaks = $isListWithLineBreaks();

        $isBadge = $isBadge();

        $isProse = $isProse();

        $arrayState = $getState();

        if ($arrayState instanceof \Illuminate\Support\Collection) {
            $arrayState = $arrayState->all();
        }

        if (is_array($arrayState)) {
            if ($listLimit = $getListLimit()) {
                $limitedArrayState = array_slice($arrayState, $listLimit);
                $arrayState = array_slice($arrayState, 0, $listLimit);
            }

            if ((! $isListWithLineBreaks) && (! $isBadge)) {
                $arrayState = implode(
                    ', ',
                    array_map(
                        fn ($value) => $value instanceof \Filament\Support\Contracts\HasLabel ? $value->getLabel() : $value,
                        $arrayState,
                    ),
                );
            }
        }
        $arrayState = \Illuminate\Support\Arr::wrap($arrayState);

        $canWrap = $canWrap();

        $iconPosition = $getIconPosition();
        $iconSize = $isBadge ? 'h-3 w-3' : 'h-4 w-4';

        $url = $getUrl();

        $isCopyable = $isCopyable();
        $copyMessage = $getCopyMessage();
        $copyMessageDuration = $getCopyMessageDuration();
    @endphp

    <x-filament-infolists::affixes
        :prefix-actions="$getPrefixActions()"
        :suffix-actions="$getSuffixActions()"
        @class([
            'filament-infolists-text-entry',
            'text-primary-600 transition hover:underline hover:text-primary-500 focus:underline focus:text-primary-500' => $url && (! $isBadge),
        ])
        :attributes="\Filament\Support\prepare_inherited_attributes($getExtraAttributeBag())"
    >
        <{{ $isListWithLineBreaks ? 'ul' : 'div' }}
            @class([
                'list-inside list-disc' => $isBulleted(),
                'flex flex-wrap gap-1' => $isBadge,
            ])
        >
            @foreach ($arrayState as $state)
                @php
                    $formattedState = $formatState($state);
                    $icon = $getIcon($state);
                @endphp

                @if (filled($formattedState))
                    <{{ $isListWithLineBreaks ? 'li' : 'div' }}>
                        <div
                            @class([
                                'inline-flex items-center space-x-1 rtl:space-x-reverse',
                                'min-h-6 justify-center whitespace-nowrap rounded-xl px-2 py-0.5' => $isBadge,
                                'prose max-w-none dark:prose-invert' => $isProse,
                                'whitespace-normal' => $canWrap,
                                ($isBadge ? match ($color = $getColor($state)) {
                                    'danger' => 'bg-danger-500/10 text-danger-700 dark:text-danger-500',
                                    'gray', null => 'bg-gray-500/10 text-gray-700 dark:bg-gray-500/20 dark:text-gray-300',
                                    'info' => 'bg-info-500/10 text-info-700 dark:text-info-500',
                                    'primary' => 'bg-primary-500/10 text-primary-700 dark:text-primary-500',
                                    'secondary' => 'bg-secondary-500/10 text-secondary-700 dark:text-secondary-500',
                                    'success' => 'bg-success-500/10 text-success-700 dark:text-success-500',
                                    'warning' => 'bg-warning-500/10 text-warning-700 dark:text-warning-500',
                                    default => $color,
                                } : null),
                                ((! ($isBadge || $url)) ? match ($color = $getColor($state)) {
                                    'danger' => 'text-danger-600',
                                    'gray' => 'text-gray-600 dark:text-gray-400',
                                    'info' => 'text-info-600',
                                    'primary' => 'text-primary-600',
                                    'secondary' => 'text-secondary-600',
                                    'success' => 'text-success-600',
                                    'warning' => 'text-warning-600',
                                    default => $color,
                                } : null),
                                ($isProse ? match ($size = $getSize($state)) {
                                    'sm' => 'prose-sm',
                                    'base', 'md', null => 'prose-base',
                                    'lg' => 'prose-lg',
                                    default => $size,
                                } : match ($size = ($isBadge ? 'sm' : $getSize($state))) {
                                    'xs' => 'text-xs',
                                    'sm' => 'text-sm',
                                    'base', 'md', null => 'text-base',
                                    'lg' => 'text-lg',
                                    default => $size,
                                }),
                                match ($weight = ($isBadge ? 'medium' : $getWeight($state))) {
                                    'thin' => 'font-thin',
                                    'extralight' => 'font-extralight',
                                    'light' => 'font-light',
                                    'medium' => 'font-medium',
                                    'semibold' => 'font-semibold',
                                    'bold' => 'font-bold',
                                    'extrabold' => 'font-extrabold',
                                    'black' => 'font-black',
                                    default => $weight,
                                },
                                match ($getFontFamily($state)) {
                                    'sans' => 'font-sans',
                                    'serif' => 'font-serif',
                                    'mono' => 'font-mono',
                                    default => null,
                                },
                            ])
                        >
                            @if ($icon && $iconPosition === 'before')
                                <x-filament::icon
                                    :name="$icon"
                                    alias="infolists::entries.text.prefix"
                                    :size="$iconSize"
                                />
                            @endif

                            <div
                                @if ($isCopyable)
                                    x-on:click="
                                        window.navigator.clipboard.writeText(@js($formattedState))
                                        $tooltip(@js($copyMessage), { timeout: @js($copyMessageDuration) })
                                    "
                                @endif
                                @class([
                                    'inline-block',
                                    'cursor-pointer' => $isCopyable,
                                ])
                            >
                                {{ $formattedState }}
                            </div>

                            @if ($icon && $iconPosition === 'after')
                                <x-filament::icon
                                    :name="$icon"
                                    alias="infolists::entries.text.suffix"
                                    :size="$iconSize"
                                />
                            @endif
                        </div>
                    </{{ $isListWithLineBreaks ? 'li' : 'div' }}>
                @endif
            @endforeach

            @if ($limitedArrayStateCount = count($limitedArrayState ?? []))
                <{{ $isListWithLineBreaks ? 'li' : 'div' }}
                    @class([
                        'text-sm' => ! $isBadge,
                        'text-xs' => $isBadge,
                    ])
                >
                    {{ trans_choice('filament-infolists::components.text.more_list_items', $limitedArrayStateCount) }}
                </{{ $isListWithLineBreaks ? 'li' : 'div' }}>
            @endif
        </{{ $isListWithLineBreaks ? 'ul' : 'div' }}>
    </x-filament-infolists::affixes>
</x-dynamic-component>
