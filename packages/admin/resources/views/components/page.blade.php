@props([
    'modals' => null,
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
            <x-filament::widgets>
                @foreach ($headerWidgets as $widget)
                    @livewire(\Livewire\Livewire::getAlias($widget))
                @endforeach
            </x-filament::widgets>
        @endif

        {{ $slot }}

        @if ($footerWidgets = $this->getFooterWidgets())
            <x-filament::widgets>
                @foreach ($footerWidgets as $widget)
                    @livewire(\Livewire\Livewire::getAlias($widget))
                @endforeach
            </x-filament::widgets>
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
