<x-filament::button
    type="submit"
    form="send"
    :disabled="($this->disabledMode !== 'aria') && blank($this->token)"
    :tooltip="($this->disabledMode === 'tooltip') ? 'Submit the form' : null"
    :aria-disabled="(($this->disabledMode === 'aria') && blank($this->token)) ? 'true' : null"
    data-testid="send"
>
    Send
</x-filament::button>
