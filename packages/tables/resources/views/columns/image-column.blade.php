<div
    {{
        $attributes
            ->merge($getExtraAttributes())
            ->class([
                'filament-tables-image-column',
                'px-4 py-3' => ! $isInline(),
            ])
    }}
>
    @php
        $height = $getHeight();
        $width = $getWidth() ?? ($isCircular() || $isSquare() ? $height : null);
    @endphp

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
                {{
                    $getExtraImgAttributeBag()->class([
                        'object-cover object-center' => $isCircular() || $isSquare(),
                    ])
                }}
            />
        @endif
    </div>
</div>
