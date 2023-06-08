<div
    {{
        $attributes
            ->merge($getExtraAttributes(), escape: false)
            ->class([
                'filament-tables-image-column',
                'px-4 py-3' => ! $isInline(),
            ])
    }}
>
    @php
        $isCircular = $isCircular();
        $isSquare = $isSquare();
        $height = $getHeight();
        $width = $getWidth() ?? ($isCircular || $isSquare ? $height : null);
    @endphp

    <div
        style="
            @if ($height) height: {{ $height }}; @endif
            @if ($width) width: {{ $width }}; @endif
        "
        @class([
            'overflow-hidden' => $isCircular || $isSquare,
            'rounded-full' => $isCircular,
        ])
    >
        @if ($path = $getImagePath())
            <img
                src="{{ $path }}"
                style="
                    @if ($height) height: {{ $height }}; @endif
                    @if ($width) width: {{ $width }}; @endif
                "
                {{
                    $getExtraImgAttributeBag()->class([
                        'object-cover object-center' => $isCircular || $isSquare,
                    ])
                }}
            />
        @endif
    </div>
</div>
