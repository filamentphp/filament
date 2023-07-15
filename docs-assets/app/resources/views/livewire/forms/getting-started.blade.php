<div class="min-h-screen p-16">
    @if (! count($this->mountedFormComponentActions))
        {{ $this->form }}
    @endif

    <x-filament-actions::modals />
</div>
