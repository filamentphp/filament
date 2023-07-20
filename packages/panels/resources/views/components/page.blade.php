@php
    use phpDocumentor\Reflection\Types\Context;

    $widgetData = $this->getWidgetData();
@endphp

<div {{ $attributes->class(['fi-page']) }}>
    {{ \Filament\Support\Facades\FilamentView::renderHook('page.start', scope: static::class) }}

    <section class="grid auto-cols-fr gap-y-8 py-8">
        @if ($header = $this->getHeader())
            {{ $header }}
        @elseif ($heading = $this->getHeading())
            <x-filament::header
                :actions="$this->getCachedHeaderActions()"
                :breadcrumbs="filament()->hasBreadcrumbs() ? $this->getBreadcrumbs() : []"
                :heading="$heading"
                :subheading="$this->getSubheading()"
            />
        @endif

        {{ \Filament\Support\Facades\FilamentView::renderHook('page.header-widgets.start', scope: static::class) }}

        @if ($headerWidgets = $this->getVisibleHeaderWidgets())
            <x-filament-widgets::widgets
                :columns="$this->getHeaderWidgetsColumns()"
                :data="$widgetData"
                :widgets="$headerWidgets"
            />
        @endif

        {{ \Filament\Support\Facades\FilamentView::renderHook('page.header-widgets.end', scope: static::class) }}

        {{ $slot }}

        {{ \Filament\Support\Facades\FilamentView::renderHook('page.footer-widgets.start', scope: static::class) }}

        @if ($footerWidgets = $this->getVisibleFooterWidgets())
            <x-filament-widgets::widgets
                :columns="$this->getFooterWidgetsColumns()"
                :data="$widgetData"
                :widgets="$footerWidgets"
            />
        @endif

        {{ \Filament\Support\Facades\FilamentView::renderHook('page.footer-widgets.end', scope: static::class) }}

        @if ($footer = $this->getFooter())
            {{ $footer }}
        @endif
    </section>

    @if (! $this instanceof \Filament\Tables\Contracts\HasTable)
        <x-filament-actions::modals />
    @endif

    {{ \Filament\Support\Facades\FilamentView::renderHook('page.end', scope: static::class) }}
</div>
