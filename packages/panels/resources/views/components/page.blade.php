@php
    $widgetData = $this->getWidgetData();
@endphp

<div {{ $attributes->class(['fi-page']) }}>
    {{ \Filament\Support\Facades\FilamentView::renderHook('page.start') }}

    <div class="space-y-6">
        @if ($header = $this->getHeader())
            {{ $header }}
        @elseif ($heading = $this->getHeading())
            <x-filament::header :actions="$this->getCachedHeaderActions()">
                <x-slot name="heading">
                    {{ $heading }}
                </x-slot>

                @if ($subheading = $this->getSubheading())
                    <x-slot name="subheading">
                        {{ $subheading }}
                    </x-slot>
                @endif
            </x-filament::header>
        @endif

        {{ \Filament\Support\Facades\FilamentView::renderHook('page.header-widgets.start') }}

        @if ($headerWidgets = $this->getVisibleHeaderWidgets())
            <x-filament-widgets::widgets
                :widgets="$headerWidgets"
                :columns="$this->getHeaderWidgetsColumns()"
                :data="$widgetData"
            />
        @endif

        {{ \Filament\Support\Facades\FilamentView::renderHook('page.header-widgets.end') }}

        {{ $slot }}

        {{ \Filament\Support\Facades\FilamentView::renderHook('page.footer-widgets.start') }}

        @if ($footerWidgets = $this->getVisibleFooterWidgets())
            <x-filament-widgets::widgets
                :widgets="$footerWidgets"
                :columns="$this->getFooterWidgetsColumns()"
                :data="$widgetData"
            />
        @endif

        {{ \Filament\Support\Facades\FilamentView::renderHook('page.footer-widgets.end') }}

        @if ($footer = $this->getFooter())
            {{ $footer }}
        @endif
    </div>

    @if (! $this instanceof \Filament\Tables\Contracts\HasTable)
        <x-filament-actions::modals />
    @endif

    {{ \Filament\Support\Facades\FilamentView::renderHook('page.end') }}
</div>
