<div {{ $attributes->merge($getExtraAttributes())->class([
    'filament-tables-image-column',
    'px-4 py-3' => ! $isInline(),
]) }}>
    @php
        $height = $getHeight();
        $width = $getWidth() ?? ($isRounded() || $isSquare() ? $height : null);
    @endphp

    <div
        style="
            {!! $height !== null ? "height: {$height};" : null !!}
            {!! $width !== null ? "width: {$width};" : null !!}
        "
        @class([
            'rounded-full overflow-hidden' => $isRounded(),
            'shadow-lg rounded-md overflow-hidden' => $isSquare()
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
                    'object-cover object-center' => $isRounded() || $isSquare(),
                ]) }}
            >
       @endif
    </div>
</div>
