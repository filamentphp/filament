<div class="px-4 py-3">
    @if ($path = $getImagePath())
        <img
            src="{{ $path }}"
            @class([
                'rounded-full' => $isRounded(),
            ])
            style="
                {!! ($height = $getHeight()) !== null ? "height: {$height};" : null !!}
                {!! ($width = $getWidth()) !== null ? "width: {$width};" : null !!}
            "
        >
    @endif
</div>
