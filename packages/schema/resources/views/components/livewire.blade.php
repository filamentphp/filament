<div>
    @if (filled($key = $getLivewireKey()))
        @livewire($getComponent(), $getComponentProperties(), key($key))
    @else
        @livewire($getComponent(), $getComponentProperties())
    @endif
</div>
