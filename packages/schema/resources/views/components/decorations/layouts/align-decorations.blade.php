@php
    use Filament\Support\Enums\Alignment;

    $alignment = $getAlignment();
    $decorations = $getDecorations();
@endphp

<div
    @class([
        'flex grow flex-wrap items-center gap-3',
        match ($alignment) {
            Alignment::Start, Alignment::Left => 'justify-start',
            Alignment::Center => 'justify-center',
            Alignment::End, Alignment::Right => 'justify-end',
            Alignment::Between, Alignment::Justify => 'justify-between',
            default => $alignment,
        },
    ])
>
    @foreach ($decorations as $decoration)
        @if (is_array($decoration))
            <div class="flex flex-wrap items-center gap-3">
                @foreach ($decoration as $groupedDecoration)
                    {{ $groupedDecoration }}
                @endforeach
            </div>
        @else
            {{ $decoration }}
        @endif
    @endforeach
</div>
