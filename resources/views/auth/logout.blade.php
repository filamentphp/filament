<button type="button"
    wire:click="submit"
    @if ($class) class="{{ $class }}" @endif
>
    {{ $label }}
</button>
