<x-dynamic-component
    :component="$getEntryWrapperView()"
    :entry="$entry"
>
    @php
        $isCircular = $isCircular();
        $isSquare = $isSquare();
        $height = $getHeight();
        $width = $getWidth() ?? ($isCircular || $isSquare ? $height : null);
    @endphp

    <div {{ $attributes->merge($getExtraAttributes())->class([
        'filament-infolists-image-entry flex',
        match ($getAlignment()) {
            'center' => 'justify-center',
            'end' => 'justify-end',
            'left' => 'justify-left',
            'right' => 'justify-right',
            'start', null => 'justify-start',
        },
    ]) }}>
        <div
            style="
                @if ($height) height: {{ $height }}; @endif
                @if ($width) width: {{ $width }}; @endif
            "
            @class([
                'inline-block',
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
                    {{ $getExtraImgAttributeBag()->class([
                        'object-cover object-center' => $isCircular || $isSquare,
                    ]) }}
                >
            @endif
        </div>
    </div>
</x-dynamic-component>
