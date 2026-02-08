@if ($this instanceof \Filament\Actions\Contracts\HasActions && (! $this->hasActionsModalRendered))
    <style nonce="{{ \Filament\csp_nonce() }}">
        .fi-internal-components-modals {height: 0;}
    </style>
    <div
        wire:partial="action-modals"
        x-data="filamentActionModals({
                    livewireId: @js($this->getId()),
                })"
        class="fi-internal-components-modals"
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
