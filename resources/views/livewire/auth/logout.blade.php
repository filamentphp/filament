<button type="button" 
    wire:click="logout" 
    @if ($class) 
        class="{{ $class }}" 
    @endif>
    {{ $label }}
</button>