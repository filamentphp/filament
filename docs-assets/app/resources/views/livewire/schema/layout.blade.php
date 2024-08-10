<div class="min-h-screen p-16">
    <div>
        @if (! count($this->mountedFormComponentActions))
            {{ $this->form }}
        @endif
    </div>

    <x-filament-actions::modals />
</div>
