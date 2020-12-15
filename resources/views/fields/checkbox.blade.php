<div class="space-y-1">
    <x-filament::label>
        <span class="inline-flex items-center space-x-2">
            <input type="{{ $type }}" 
                name="{{ $name }}"
                @if ($model)
                    {{ $modelDirective }}="{{ $model }}"
                @endif
                @isset($checked)
                    checked
                @endisset
                @if ($extraAttributes)
                    @foreach ($extraAttributes as $attribute => $value)
                        {{ $attribute }}="{{ $value }}"
                    @endforeach
                @endif
                class="rounded text-blue-700 shadow-sm focus:border-blue-700 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error($model ?? $name) border-red-600 @else border-gray-300 @enderror" />
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
    <x-filament::error :field="$model ?? $name" />
    @if ($help)
        <x-filament::help>
            @markdown($help)
        </x-filament::help>
    @endif
</div>