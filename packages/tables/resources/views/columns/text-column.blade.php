@php
    $descriptionAbove = $getDescriptionAbove();
    $descriptionBelow = $getDescriptionBelow();
@endphp

<div
    {{ $attributes->merge($getExtraAttributes())->class([
        'filament-tables-text-column px-4 py-3',
        'text-primary-600 transition hover:underline hover:text-primary-500 focus:underline focus:text-primary-500' => $getAction() || $getUrl(),
        'whitespace-normal' => $canWrap(),
    ]) }}
>
    @if (filled($descriptionAbove))
        <span class="block text-sm text-gray-400">
            {{ $descriptionAbove instanceof \Illuminate\Support\HtmlString ? $descriptionAbove : \Illuminate\Support\Str::of($descriptionAbove)->markdown()->sanitizeHtml()->toHtmlString() }}
        </span>
    @endif

    {{ $getFormattedState() }}

    @if (filled($descriptionBelow))
        <span class="block text-sm text-gray-400">
            {{ $descriptionBelow instanceof \Illuminate\Support\HtmlString ? $descriptionBelow : \Illuminate\Support\Str::of($descriptionBelow)->markdown()->sanitizeHtml()->toHtmlString() }}
        </span>
    @endif
</div>
