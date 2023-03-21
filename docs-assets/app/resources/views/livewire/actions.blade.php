<div class="min-h-screen">
    @if (! $this->mountedAction)
        <div id="buttonAction" class="p-16 flex items-center justify-center max-w-xl">
            {{ $this->buttonAction }}
        </div>

        <div id="linkAction" class="p-16 flex items-center justify-center max-w-xl">
            {{ $this->linkAction }}
        </div>

        <div id="iconButtonAction" class="p-16 flex items-center justify-center max-w-xl">
            {{ $this->iconButtonAction }}
        </div>

        <div id="dangerAction" class="p-16 flex items-center justify-center max-w-xl">
            {{ $this->dangerAction }}
        </div>

        <div id="largeAction" class="p-16 flex items-center justify-center max-w-xl">
            {{ $this->largeAction }}
        </div>

        <div id="iconAction" class="p-16 flex items-center justify-center max-w-xl">
            {{ $this->iconAction }}
        </div>

        <div id="iconAfterAction" class="p-16 flex items-center justify-center max-w-xl">
            {{ $this->iconAfterAction }}
        </div>

        <div id="indicatorAction" class="p-16 flex items-center justify-center max-w-xl">
            {{ $this->indicatorAction }}
        </div>

        <div id="successIndicatorAction" class="p-16 flex items-center justify-center max-w-xl">
            {{ $this->successIndicatorAction }}
        </div>

        <div id="outlinedAction" class="p-16 flex items-center justify-center max-w-xl">
            {{ $this->outlinedAction }}
        </div>

        <div id="inlineIconAction" class="p-16 flex items-center justify-center max-w-xl">
            {{ $this->inlineIconAction }}
        </div>

        <div id="confirmationModalAction" class="p-16 flex items-center justify-center max-w-xl">
            {{ $this->confirmationModalAction }}
        </div>

        <div id="confirmationModalCustomTextAction" class="p-16 flex items-center justify-center max-w-xl">
            {{ $this->confirmationModalCustomTextAction }}
        </div>

        <div id="modalFormAction" class="p-16 flex items-center justify-center max-w-xl">
            {{ $this->modalFormAction }}
        </div>

        <div id="slideOverAction" class="p-16 flex items-center justify-center max-w-xl">
            {{ $this->slideOverAction }}
        </div>
    @endif

    <div id="modal">
        <x-filament-actions::modals />
    </div>
</div>
