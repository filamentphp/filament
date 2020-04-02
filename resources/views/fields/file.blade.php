<fieldset class="mb-5">
    @if ($field->label)
        <legend class="block mb-2 text-sm font-medium leading-5 text-gray-700 dark:text-gray-50">
            {{ __($field->label) }}
            @if ($field->required)
                <sup class="text-red-600">*</sup>
                <span class="sr-only">(required)</span>
            @endif
        </legend>
    @endif
    <label class="relative block mb-2">
        <input
            name="{{ $field->name }}"
            type="file"
            class="
                form-input dark:text-gray-50 dark:bg-gray-900 dark:border-gray-700 block w-full sm:text-sm sm:leading-5 
                @error($field->key)
                    pr-10 border-red-300 dark:border-red-500 text-red-900 placeholder-red-500 dark:placeholder-red-500 focus:border-red-500 dark-focus:border-red-500 focus:shadow-outline-red dark-focus:shadow-outline-red
                @enderror
            "
            {{ $field->file_multiple ? 'multiple' : '' }}
            data-validation-rules='@json($field->file_rules)'
            data-validation-messages='@json($field->file_validation_messages)'
        >
        @error($field->key) 
            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                {{ Filament::svg('heroicons/solid-sm/sm-exclamation-circle', 'h-5 w-5 text-red-600') }}
            </div>
        @enderror
        {{ $field->placeholder }}
    </label>
    @if ($this->form_data[$field->name])
        <ul class="mb-2">
            @foreach ($this->form_data[$field->name] as $key => $value)
                <li class="text-sm leading-5 bg-gray-50 dark:bg-gray-700 rounded overflow-hidden shadow flex items-center justify-between">
                    <a href="{{ Storage::url($value['file']) }}" 
                        target="_blank" 
                        rel="noopener noreferrer"
                        class="p-2 flex-grow flex items-center mr-2"
                    >
                        {{ Filament::svg($this->fileIcon($value['mime_type']), 'flex-shrink-0 w-5 h-5 mr-2') }}
                        <span class="flex-grow">
                            {{ $value['name'] }}
                        </span>
                    </a>
                    <button type="button"
                        onclick="confirm('{{ __('Are you sure?') }}') || event.stopImmediatePropagation();"
                        wire:click.prevent="arrayRemove('{{ $field->name }}', '{{ $key }}')"
                        class="flex-shrink-0 flex items-center p-2"
                    >
                        {{ Filament::svg('heroicons/outline-md/md-x', 'h-4 w-4 text-red-500') }}
                        <span class="sr-only">{{ __('filament::admin.remove', ['item' => $value['name']]) }}</span>
                    </button>
                </li>
            @endforeach
        </ul>
    @endif
    @include('filament::fields.error-help')
</fieldset>
