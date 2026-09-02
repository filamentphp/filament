@props([
    'heading' => null,
    'subheading' => null,
])

@php
    use Filament\Support\Facades\FilamentView;
    use Filament\Tables\Contracts\HasTable;
    use Filament\View\PanelsRenderHook;

    $heading ??= $this->getHeading();
    $subheading ??= $this->getSubHeading();
    $hasLogo = $this->hasLogo();
@endphp

<div {{ $attributes->class(['fi-simple-page']) }}>
    {{ FilamentView::renderHook(PanelsRenderHook::SIMPLE_PAGE_START, scopes: $this->getRenderHookScopes()) }}

    <div class="fi-simple-page-content">
        @if (filled($heading) || $hasLogo || filled($subheading))
            <x-filament-panels::header.simple
                :heading="$heading"
                :logo="$hasLogo"
                :subheading="$subheading"
            />
        @endif

        {{ $slot }}
    </div>

    @if (! $this instanceof HasTable)
        <x-filament-actions::modals />
    @endif

    {{ FilamentView::renderHook(PanelsRenderHook::SIMPLE_PAGE_END, scopes: $this->getRenderHookScopes()) }}
</div>
