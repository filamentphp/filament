<div {{ $attributes->merge($getExtraAttributes())->class([
    'filament-tables-image-column',
    'px-4 py-3' => ! $isInline(),
]) }}>
    @php
        $height = $getHeight();
        $width = $getWidth() ?? ($isCircular() || $isSquare() ? $height : null);
        $overlap = $getOverlap() ?? 'sm';
        $imageCount = 0;

        $ring = match ($getRing()) {
            0 => 'ring-0',
            1 => 'ring-1',
            2 => 'ring-2',
            4 => 'ring-4',
            default => 'ring',
        };

        $remainingTextSize = match ($getRemainingTextSize()) {
            'xs' => 'text-xs',
            'sm' => 'text-sm',
            'md' => 'text-md',
            'lg' => 'text-lg',
            default => 'text-sm',
        };
    @endphp

    @if ($isStacked())
        <div class="flex items-center space-x-2">
            <div 
                @class([
                    'flex',
                    match ($overlap) {
                        0 => 'space-x-0',
                        1 => '-space-x-1',
                        2 => '-space-x-2',
                        3 => '-space-x-3',
                        4 => '-space-x-4',
                        default => '-space-x-1',
                    },
                ])
            >
                @foreach (array_slice($getImages(), 0, $getLimit()) as $image)
                    @if ($path = $getStackedImagePath($image))
                        @php
                            $imageCount ++;
                        @endphp
                        <img
                            src="{{ $path }}"
                            style="
                                {!! $height !== null ? "height: {$height};" : null !!}
                                {!! $width !== null ? "width: {$width};" : null !!}
                            "
                            {{ $getExtraImgAttributeBag()->class([
                                'max-w-none ring-white',
                                'dark:ring-gray-800' => config('tables.dark_mode'),
                                'rounded-full' => $isCircular(),
                                $ring,
                            ]) }}
                        >
                    @endif
                @endforeach

                @if ($shouldShowRemaining() && (! $shouldShowRemainingAfterStack()) && ($imageCount < count($getImages())))
                    <div 
                        style="
                            {!! $height !== null ? "height: {$height};" : null !!}
                            {!! $width !== null ? "width: {$width};" : null !!}
                        "
                        @class([
                            'flex items-center justify-center bg-gray-100 text-gray-500 ring-white',
                            'dark:bg-gray-600 dark:text-gray-300 dark:ring-gray-800' => config('tables.dark_mode'),
                            'rounded-full' => $isCircular(),
                            $remainingTextSize,
                            $ring,
                        ])
                    >
                        <span class="-ml-1">
                            +{{ count($getImages()) - $imageCount }}
                        </span>
                    </div>
                @endif

            </div>
            
            @if ($shouldShowRemaining() && $shouldShowRemainingAfterStack() && ($imageCount < count($getImages())))
                <div 
                    @class([
                        'text-gray-500',
                        'dark:text-gray-300' => config('tables.dark_mode'),
                        $remainingTextSize,
                    ])
                >
                    +{{ count($getImages()) - $imageCount }}
                </div>
            @endif

        </div>
    @else
        <div
            style="
                {!! $height !== null ? "height: {$height};" : null !!}
                {!! $width !== null ? "width: {$width};" : null !!}
            "
            @class([
                'overflow-hidden' => $isCircular() || $isSquare(),
                'rounded-full' => $isCircular(),
            ])
        >
            @if ($path = $getImagePath())
                <img
                    src="{{ $path }}"
                    style="
                        {!! $height !== null ? "height: {$height};" : null !!}
                        {!! $width !== null ? "width: {$width};" : null !!}
                    "
                    {{ $getExtraImgAttributeBag()->class([
                        'object-cover object-center' => $isCircular() || $isSquare(),
                    ]) }}
                >
            @endif
        </div>
    @endif
</div>
