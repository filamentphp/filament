@php
    use Filament\Support\Enums\VerticalAlignment;

    $verticalAlignment = $getVerticalAlignment();

    if (! $verticalAlignment instanceof VerticalAlignment) {
        $verticalAlignment = filled($verticalAlignment) ? (VerticalAlignment::tryFrom($verticalAlignment) ?? $verticalAlignment) : null;
    }
@endphp

<div
    {{
        $attributes
            ->merge([
                'id' => $getId(),
            ], escape: false)
            ->merge($getExtraAttributes(), escape: false)
            ->class([
                'fi-fo-actions flex h-full flex-col',
                match ($verticalAlignment) {
                    VerticalAlignment::Start => 'justify-start',
                    VerticalAlignment::Center => 'justify-center',
                    VerticalAlignment::End => 'justify-end',
                    default => $verticalAlignment,
                },
            ])
    }}
>
    <x-filament::actions
        :actions="$getChildComponentContainer()->getComponents()"
        :alignment="$getAlignment()"
        :full-width="$isFullWidth()"
    />
</div>
