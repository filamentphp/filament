<div {{ $attributes->merge($getExtraAttributes())->class(['filament-tables-image-column px-4 py-3']) }}>
    @php
        $height = $getHeight();
        $width = $getWidth() ?? ($isRounded() ? $height : null);
    @endphp

    <div
        style="
            {!! $height !== null ? "height: {$height};" : null !!}
            {!! $width !== null ? "width: {$width};" : null !!}
        "
        @class(['rounded-full overflow-hidden' => $isRounded()])
    >
        @if ($path = $getImagePath())
            <img
                src="{{ $path }}"
                style="
                    {!! $height !== null ? "height: {$height};" : null !!}
                    {!! $width !== null ? "width: {$width};" : null !!}
                "
                {{ $getExtraImgAttributeBag()->class([
                    'object-cover object-center' => $isRounded(),
                ]) }}
            >
       @endif
    </div>
</div>
