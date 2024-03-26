@php
    $headerActions = array_filter(
        $getHeaderActions(),
        fn ($headerAction): bool => $headerAction->isVisible(),
    );

    $footerActions = array_filter(
        $getFooterActions(),
        fn ($footerAction): bool => $footerAction->isVisible(),
    );

    $hasHeaderActions = filled($headerActions);

    $hasFooterActions = filled($footerActions);

    $footerActionsAlignment = $getFooterActionsAlignment();
@endphp

<form
    {{
        $attributes
            ->merge([
                'id' => $getId(),
                'wire:submit' => $getLivewireSubmitHandler(),
            ], escape: false)
            ->merge($getExtraAttributes(), escape: false)
            ->class(['fi-fo-form flex-col gap-6'])
    }}
>
    @if ($hasHeaderActions)
        <x-filament::actions
            :actions="$headerActions"
            :alignment="\Filament\Support\Enums\Alignment::Start"
        />
    @endif

    {{ $getChildComponentContainer() }}

    @if ($hasFooterActions)
        <x-filament::actions
            :actions="$footerActions"
            :alignment="$footerActionsAlignment"
        />
    @endif
</form>
