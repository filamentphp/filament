@if ($this instanceof \Filament\Actions\Contracts\HasActions && (! $this->hasActionsModalRendered))
    <div
        wire:partial="action-modals"
        x-data="filamentActionModals({
                    livewireId: @js($this->getId()),
                })"
        style="height: 0"
    >
        @foreach ($this->getMountedActions() as $action)
            @if ((! $loop->last) || $this->mountedActionShouldOpenModal())
                {{ $action->toModalHtmlable() }}
            @endif
        @endforeach
    </div>

    @php
        $this->hasActionsModalRendered = true;
    @endphp
@endif
