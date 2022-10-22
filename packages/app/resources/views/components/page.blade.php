@props([
    'widgetData' => [],
])

<div {{ $attributes->class(['filament-page']) }}>
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

        {{ \Filament\Facades\Filament::renderHook('page.header-widgets.start') }}

        @if ($headerWidgets = $this->getHeaderWidgets())
            <x-filament-widgets::widgets
                :widgets="$headerWidgets"
                :columns="$this->getHeaderWidgetsColumns()"
                :data="$widgetData"
            />
        @endif

        {{ \Filament\Facades\Filament::renderHook('page.header-widgets.end') }}

        {{ $slot }}

        {{ \Filament\Facades\Filament::renderHook('page.footer-widgets.start') }}

        @if ($footerWidgets = $this->getFooterWidgets())
            <x-filament-widgets::widgets
                :widgets="$footerWidgets"
                :columns="$this->getFooterWidgetsColumns()"
                :data="$widgetData"
            />
        @endif

        {{ \Filament\Facades\Filament::renderHook('page.footer-widgets.end') }}

        @if ($footer = $this->getFooter())
            {{ $footer }}
        @endif
    </div>

    {{ $this->actionsModal }}
    {{ $this->formsModal }}
</div>
