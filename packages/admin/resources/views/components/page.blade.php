@props([
    'modals' => null,
    'widgetRecord' => null,
])

<div {{ $attributes }}>
    <div class="space-y-6">
        @if ($header = $this->getHeader())
            {{ $header }}
        @elseif ($heading = $this->getHeading())
            <x-filament::header :actions="$this->getActions()">
                <x-slot name="heading">
                    {{ $heading }}
                </x-slot>
            </x-filament::header>
        @endif

        @if ($headerWidgets = $this->getHeaderWidgets())
            <x-filament::widgets :widgets="$headerWidgets" :data="['record' => $widgetRecord]" />
        @endif

        {{ $slot }}

        @if ($footerWidgets = $this->getFooterWidgets())
            <x-filament::widgets :widgets="$footerWidgets" :data="['record' => $widgetRecord]" />
        @endif

        @if ($footer = $this->getFooter())
            {{ $footer }}
        @endif
    </div>

    {{ $modals }}

    @if ($notification = session()->get('notification'))
        <x-filament::notification :message="$notification['message']" :status="$notification['status']" />
    @endif
</div>
