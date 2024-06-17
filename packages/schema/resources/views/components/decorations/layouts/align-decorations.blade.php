@php
    $startDecorations = $getStartDecorations();
    $endDecorations = $getEndDecorations();

    $isBetween = $startDecorations && $endDecorations;
@endphp

<div
    @class([
        'flex grow flex-wrap items-center gap-x-3',
        'gap-y-1' => ! $isBetween,
        'justify-start' => (! $isBetween) && $startDecorations,
        'justify-end' => (! $isBetween) && $endDecorations,
        'justify-between' => $isBetween,
    ])
>
    @if ($isBetween)
        <div class="flex flex-wrap items-center gap-x-3 gap-y-1">
            @foreach ($startDecorations as $decoration)
                {{ $decoration }}
            @endforeach
        </div>

        <div class="flex flex-wrap items-center gap-x-3 gap-y-1">
            @foreach ($endDecorations as $decoration)
                {{ $decoration }}
            @endforeach
        </div>
    @elseif ($startDecorations)
        @foreach ($startDecorations as $decoration)
            {{ $decoration }}
        @endforeach
    @elseif ($endDecorations)
        @foreach ($endDecorations as $decoration)
            {{ $decoration }}
        @endforeach
    @endif
</div>
