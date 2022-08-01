<div
    {{ $attributes->merge($getExtraAttributes())->class([
        'px-4 py-3 filament-tables-text-column',
        'text-primary-600 transition hover:underline hover:text-primary-500 focus:underline focus:text-primary-500' => $getAction() || $getUrl(),
        'whitespace-normal' => $canWrap(),
    ]) }}
>
    @if(($showDescriptionOnTop = $getShowDescriptionOnTop()) && ($description = $getDescription()))
        <span class="block text-sm text-gray-400"> {!! \Illuminate\Support\Str::of($description)->markdown()->sanitizeHtml() !!}</span>
    @endif

    {{ $getFormattedState() }}

    @if(! $showDescriptionOnTop && ($description = $getDescription()))
        <span class="block text-sm text-gray-400">{!! \Illuminate\Support\Str::of($description)->markdown()->sanitizeHtml() !!}</span>
    @endif
</div>
