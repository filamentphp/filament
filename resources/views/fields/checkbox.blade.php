<div class="space-y-1">
    <x-filament::label>
        <span class="inline-flex items-center space-x-2">
            <input type="{{ $type }}" 
                {{ $modelDirective }}="{{ $model }}"
                value="{{ $value }}"
                @foreach ($extraAttributes as $attribute => $value)
                    {{ $attribute }}="{{ $value }}"
                @endforeach
                class="rounded text-blue-700 shadow-sm focus:border-blue-700 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error($model) border-red-600 @else border-gray-300 @enderror" />
            <span class="inline-flex items-baseline space-x-4">
                @if ($label)
                    <span>{{ $label }}</span>
                @endif
                @if ($hint)
                    <x-filament::hint>
                        @markdown($hint)
                    </x-filament::hint>
                @endif       
            </span>
        </span>
    </x-filament::label>
    @if ($showErrors)
        <x-filament::error :field="$model" />
    @endif
    @if ($help)
        <x-filament::help>
            @markdown($help)
        </x-filament::help>
    @endif
</div>