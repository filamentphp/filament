<x-dynamic-component
    :component="$getEntryWrapperView()"
    :entry="$entry"
>
    @php
        $isListWithLineBreaks = $isListWithLineBreaks();

        $isBadge = $isBadge();

        $isProse = $isProse();

        $arrayState = $getState();
        if (is_array($arrayState)) {
            if ($listLimit = $getListLimit()) {
                $limitedArrayState = array_slice($arrayState, $listLimit);
                $arrayState = array_slice($arrayState, 0, $listLimit);
            }

            if ((! $isListWithLineBreaks) && (! $isBadge)) {
                $arrayState = implode(', ', $arrayState);
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

    <div {{ $attributes
        ->merge($getExtraAttributes(), escape: false)
        ->class([
            'filament-infolists-text-entry',
            'text-primary-600 transition hover:underline hover:text-primary-500 focus:underline focus:text-primary-500' => $url && (! $isBadge),
        ])
    }}>
        <{{ $isListWithLineBreaks ? 'ul' : 'div' }} @class([
            'list-disc list-inside' => $isBulleted(),
            'flex flex-wrap gap-1' => $isBadge,
        ])>
            @foreach ($arrayState as $state)
                @php
                    $formattedState = $formatState($state);
                    $icon = $getIcon($state);
                @endphp

                @if (filled($formattedState))
                    <{{ $isListWithLineBreaks ? 'li' : 'div' }}>
                        <div @class([
                            'inline-flex items-center space-x-1 rtl:space-x-reverse',
                            'justify-center min-h-6 px-2 py-0.5 rounded-xl whitespace-nowrap' => $isBadge,
                            'prose max-w-none' => $isProse,
                            'whitespace-normal' => $canWrap,
                            ($isBadge ? match ($color = $getColor($state)) {
                                'danger' => 'text-danger-700 bg-danger-500/10 dark:text-danger-500',
                                'gray', null => 'text-gray-700 bg-gray-500/10 dark:text-gray-300 dark:bg-gray-500/20',
                                'primary' => 'text-primary-700 bg-primary-500/10 dark:text-primary-500',
                                'secondary' => 'text-secondary-700 bg-secondary-500/10 dark:text-secondary-500',
                                'success' => 'text-success-700 bg-success-500/10 dark:text-success-500',
                                'warning' => 'text-warning-700 bg-warning-500/10 dark:text-warning-500',
                                default => $color,
                            } : null),
                            ((! ($isBadge || $url)) ? match ($color = $getColor($state)) {
                                'danger' => 'text-danger-600',
                                'gray' => 'text-gray-600 dark:text-gray-400',
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
                        ])>
                            @if ($icon && $iconPosition === 'before')
                                <x-filament::icon
                                    :name="$icon"
                                    alias="filament-infolists::entries.text.prefix"
                                    :size="$iconSize"
                                />
                            @endif

                            @if ($isCopyable)
                                <span
                                    x-on:click="
                                        window.navigator.clipboard.writeText(@js($formattedState))
                                        $tooltip(@js($copyMessage), { timeout: @js($copyMessageDuration) })
                                    "
                                    class="cursor-pointer"
                                >
                            @endif

                            {{ $formattedState }}

                            @if ($isCopyable)
                                </span>
                            @endif

                            @if ($icon && $iconPosition === 'after')
                                <x-filament::icon
                                    :name="$icon"
                                    alias="filament-infolists::entries.text.suffix"
                                    :size="$iconSize"
                                />
                            @endif
                        </div>
                    </{{ $isListWithLineBreaks ? 'li' : 'div' }}>
              @endif
            @endforeach

            @if ($limitedArrayStateCount = count($limitedArrayState ?? []))
                <{{ $isListWithLineBreaks ? 'li' : 'div' }} @class([
                    'text-sm' => ! $isBadge,
                    'text-xs' => $isBadge,
                ])>
                    {{ trans_choice('filament-infolists::components.text.more_list_items', $limitedArrayStateCount) }}
                </{{ $isListWithLineBreaks ? 'li' : 'div' }}>
            @endif
        </{{ $isListWithLineBreaks ? 'ul' : 'div' }}>
    </div>
</x-dynamic-component>
