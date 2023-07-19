<x-dynamic-component :component="$getEntryWrapperView()" :entry="$entry">
    @php
        $limit = $getLimit();
        $state = collect($getState())->take($limit)->all();
        $isCircular = $isCircular();
        $isSquare = $isSquare();
        $height = $getHeight();
        $width = $getWidth() ?? ($isCircular || $isSquare ? $height : null);
        $overlap = $isStacked() ? ($getOverlap() ?? 4) : null;

        $defaultImageUrl = $getDefaultImageUrl();
        if ((! count($state)) && filled($defaultImageUrl)) {
            $state = [null];
        }

        $ringClasses = match ($getRing()) {
            0 => '',
            1 => 'ring-1',
            2 => 'ring-2',
            3 => 'ring-3',
            4 => 'ring-4',
            5 => 'ring-5',
            6 => 'ring-6',
            7 => 'ring-7',
            8 => 'ring-8',
            default => 'ring',
        };

        $hasLimitedRemainingText = $hasLimitedRemainingText();
        $isLimitedRemainingTextSeparate = $isLimitedRemainingTextSeparate();
        $limitedRemainingTextSizeClasses = match ($getLimitedRemainingTextSize()) {
            'xs' => 'text-xs',
            'base', 'md' => 'text-base',
            'lg' => 'text-lg',
            default => 'text-sm',
        };
    @endphp

    @if (count($state))
        <div class="flex items-center gap-2">
            <div
                @class([
                    'flex',
                    match ($overlap) {
                        0 => '',
                        1 => '-space-x-1',
                        2 => '-space-x-2',
                        3 => '-space-x-3',
                        4 => '-space-x-4',
                        5 => '-space-x-5',
                        6 => '-space-x-6',
                        7 => '-space-x-7',
                        8 => '-space-x-8',
                        default => 'space-x-1',
                    },
                ])
            >
                @foreach ($state as $stateItem)
                    <img
                        src="{{ filled($stateItem) ? $getImageUrl($stateItem) : $defaultImageUrl }}"
                        style="
                            @if ($height) height: {{ $height }}; @endif
                            @if ($width) width: {{ $width }}; @endif
                        "
                        {{
                            $getExtraImgAttributeBag()->class([
                                'max-w-none object-cover object-center ring-white dark:ring-gray-900',
                                'rounded-full' => $isCircular,
                                $ringClasses,
                            ])
                        }}
                    />
                @endforeach

                @if ($hasLimitedRemainingText && ($loop->iteration < count($state)) && (! $isLimitedRemainingTextSeparate) && $isCircular)
                    <div
                        style="
                            @if ($height) height: {{ $height }}; @endif
                            @if ($width) width: {{ $width }}; @endif
                        "
                        @class([
                            'flex items-center justify-center bg-gray-100 text-gray-500 ring-white dark:bg-gray-800 dark:text-gray-300 dark:ring-gray-900',
                            'rounded-full' => $isCircular,
                            $limitedRemainingTextSizeClasses,
                            $ringClasses,
                        ])
                    >
                        <span class="-ms-1">
                            +{{ count($state) - $loop->iteration }}
                        </span>
                    </div>
                @endif
            </div>

            @if ($hasLimitedRemainingText && ($loop->iteration < count($state)) && ($isLimitedRemainingTextSeparate || (! $isCircular)))
                <div
                    @class([
                        'text-gray-600 dark:text-gray-300',
                        $limitedRemainingTextSizeClasses,
                    ])
                >
                    +{{ count($state) - $loop->iteration }}
                </div>
            @endif
        </div>
    @endif
</x-dynamic-component>
