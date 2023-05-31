@php
    $widgetData = $this->getWidgetData();
@endphp

<div {{ $attributes->class(['filament-page']) }}>
    {{ filament()->renderHook('page.start') }}

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

        {{ filament()->renderHook('page.header-widgets.start') }}

        @if ($headerWidgets = $this->getVisibleHeaderWidgets())
            <x-filament-widgets::widgets
                :widgets="$headerWidgets"
                :columns="$this->getHeaderWidgetsColumns()"
                :data="$widgetData"
            />
        @endif

        {{ filament()->renderHook('page.header-widgets.end') }}

        {{ $slot }}

        {{ filament()->renderHook('page.footer-widgets.start') }}

        @if ($footerWidgets = $this->getVisibleFooterWidgets())
            <x-filament-widgets::widgets
                :widgets="$footerWidgets"
                :columns="$this->getFooterWidgetsColumns()"
                :data="$widgetData"
            />
        @endif

        {{ filament()->renderHook('page.footer-widgets.end') }}

        @if ($footer = $this->getFooter())
            {{ $footer }}
        @endif
    </div>

    @if (! $this instanceof \Filament\Tables\Contracts\HasTable)
        <x-filament-actions::modals />
    @endif

    {{ filament()->renderHook('page.end') }}
</div>
