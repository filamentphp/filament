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

        {{ $slot }}

        @if ($footer = $this->getFooter())
            {{ $footer }}
        @endif
    </div>

    {{ $modals }}

    @if ($notification = session()->get('notification'))
        <x-filament::notification :message="$notification['message']" :status="$notification['status']" />
    @endif
</div>
