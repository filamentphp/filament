<div class="space-y-1">
    <x-filament::label>
        <span class="inline-flex items-center space-x-2">
            <input type="{{ $field->type }}" 
                {{ $field->modelDirective }}="{{ $field->model }}"
                value="{{ $field->value }}"
                @foreach ($field->extraAttributes as $attribute => $value)
                    {{ $attribute }}="{{ $value }}"
                @endforeach
                class="{{ $field->type === 'radio' ? 'rounded-full' : 'rounded' }} text-blue-700 shadow-sm focus:border-blue-700 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error($field->error ?? $field->model) border-red-600 @else border-gray-300 @enderror" />
            <span class="inline-flex items-baseline space-x-4">
                @if ($field->label)
                    <span>{{ $field->label }}</span>
                @endif
                @if ($field->hint)
                    <x-filament::hint>
                        @markdown($field->hint)
                    </x-filament::hint>
                @endif       
            </span>
        </span>
    </x-filament::label>
    @if ($field->showErrors)
        <x-filament::error :field="$field->model" />
    @endif
    @if ($field->help)
        <x-filament::help>
            @markdown($field->help)
        </x-filament::help>
    @endif
</div>