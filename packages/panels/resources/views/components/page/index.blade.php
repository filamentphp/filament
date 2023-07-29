@php
    use phpDocumentor\Reflection\Types\Context;

    $widgetData = $this->getWidgetData();
@endphp

<div {{ $attributes->class(['fi-page']) }}>
    {{ \Filament\Support\Facades\FilamentView::renderHook('panels::page.start', scopes: $this->getRenderHookScopes()) }}

    <section class="grid auto-cols-fr gap-y-8 py-8">
        @if ($header = $this->getHeader())
            {{ $header }}
        @elseif ($heading = $this->getHeading())
            <x-filament-panels::header
                :actions="$this->getCachedHeaderActions()"
                :breadcrumbs="filament()->hasBreadcrumbs() ? $this->getBreadcrumbs() : []"
                :heading="$heading"
                :subheading="$this->getSubheading()"
            />
        @endif

        {{ \Filament\Support\Facades\FilamentView::renderHook('panels::page.header-widgets.before', scopes: $this->getRenderHookScopes()) }}

        @if ($headerWidgets = $this->getVisibleHeaderWidgets())
            <x-filament-widgets::widgets
                :columns="$this->getHeaderWidgetsColumns()"
                :data="$widgetData"
                :widgets="$headerWidgets"
            />
        @endif

        {{ \Filament\Support\Facades\FilamentView::renderHook('panels::page.header-widgets.after', scopes: $this->getRenderHookScopes()) }}

        {{ $slot }}

        {{ \Filament\Support\Facades\FilamentView::renderHook('panels::page.footer-widgets.before', scopes: $this->getRenderHookScopes()) }}

        @if ($footerWidgets = $this->getVisibleFooterWidgets())
            <x-filament-widgets::widgets
                :columns="$this->getFooterWidgetsColumns()"
                :data="$widgetData"
                :widgets="$footerWidgets"
            />
        @endif

        {{ \Filament\Support\Facades\FilamentView::renderHook('panels::page.footer-widgets.after', scopes: $this->getRenderHookScopes()) }}

        @if ($footer = $this->getFooter())
            {{ $footer }}
        @endif
    </section>

    @if (! $this instanceof \Filament\Tables\Contracts\HasTable)
        <x-filament-actions::modals />
    @endif

    {{ \Filament\Support\Facades\FilamentView::renderHook('panels::page.end', scopes: $this->getRenderHookScopes()) }}
</div>
