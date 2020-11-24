@props([
    'name',
    'hint' => false,
    'help' => false,
    'hasError' => $errors->has($name) ? ' text-red-600' : '',
])

<div class="space-y-1">
    <x-filament::label>
        <span class="inline-flex items-center space-x-2">
            <input type="checkbox" name="{{ $name }}" {{ $attributes->merge(['class' => 'rounded border-gray-300 text-gray-600 shadow-sm focus:border-gray-600 focus:ring focus:ring-indigo-200 focus:ring-opacity-50'.$hasError]) }}{{ old($name) ? ' checked' : '' }}>
            <span class="inline-flex items-baseline space-x-4">
                <span>{{ $slot }}</span>
                @if ($help)
                    <x-filament::hint>
                        {{ $hint }}
                    </x-filament::hint>
                @endif        
            </span>
        </span>
    </x-filament::label>
    <x-filament::error :name="$name" />
    @if ($help)
        <x-filament::help class="inline-block">
            {{ $help }}
        </x-filament::help>
    @endif
</div>
