@php
    use Filament\Support\Enums\VerticalAlignment;
@endphp

<div
    {{
        $attributes
            ->merge([
                'id' => $getId(),
            ], escape: false)
            ->merge($getExtraAttributes(), escape: false)
            ->class([
                'fi-in-actions flex h-full flex-col',
                match ($verticalAlignment = $getVerticalAlignment()) {
                    VerticalAlignment::Center, 'center' => 'justify-center',
                    VerticalAlignment::End, 'end' => 'justify-end',
                    VerticalAlignment::Start, 'start' => 'justify-start',
                    default => $verticalAlignment,
                },
            ])
    }}
>
    <x-filament-actions::actions
        :actions="$getChildComponentContainer()->getComponents()"
        :alignment="$getAlignment()"
        :full-width="$isFullWidth()"
    />
</div>
