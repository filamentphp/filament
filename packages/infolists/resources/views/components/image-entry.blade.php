<x-dynamic-component :component="$getEntryWrapperView()" :entry="$entry">
    @php
        $height = $getHeight();
        $isCircular = $isCircular();
        $isSquare = $isSquare();
        $path = $getImageUrl();
        $width = $getWidth() ?? (($isCircular || $isSquare) ? $height : null);
    @endphp

    <div
        {{
            $attributes
                ->merge($getExtraAttributes(), escape: false)
                ->class([
                    'fi-in-image flex',
                    match ($getAlignment()) {
                        'center' => 'justify-center',
                        'end' => 'justify-end',
                        'left' => 'justify-left',
                        'right' => 'justify-right',
                        'start', null => 'justify-start',
                    },
                ])
        }}
    >
        <div
            @class([
                'inline-block',
                'overflow-hidden' => $isCircular || $isSquare,
                'rounded-full' => $isCircular,
            ])
            @style([
                "height: {$height}" => $height,
                "width: {$width}" => $width,
            ])
        >
            @if ($path)
                <img
                    src="{{ $path }}"
                    {{
                        $getExtraImgAttributeBag()
                            ->class([
                                'object-cover object-center' => $isCircular || $isSquare,
                            ])
                            ->style([
                                "height: {$height}" => $height,
                                "width: {$width}" => $width,
                            ])
                    }}
                />
            @endif
        </div>
    </div>
</x-dynamic-component>
