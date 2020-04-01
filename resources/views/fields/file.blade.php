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
            class="form-input dark:text-gray-50 dark:bg-gray-900 dark:border-gray-700 block w-full sm:text-sm sm:leading-5 
                @error($field->key)
                    pr-10 border-red-300 dark:border-red-500 text-red-900 placeholder-red-500 dark:placeholder-red-500 focus:border-red-500 dark-focus:border-red-500 focus:shadow-outline-red dark-focus:shadow-outline-red
                @enderror"
            {{ $field->file_multiple ? 'multiple' : '' }}
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
                <li>
                    <a href="{{ Storage::url($value['file']) }}" target="_blank" rel="noopener noreferrer">
                        {{ $this->fileIcon($value['mime_type']) }}
                        {{ $value['name'] }}
                    </a>
                    <button onclick="confirm('{{ __('Are you sure?') }}')"
                        wire:click.prevent="arrayRemove('{{ $field->name }}', '{{ $key }}')">
                        remove
                    </button>
                </li>
            @endforeach
        </ul>
        {{ var_dump($this->form_data[$field->name]) }}
    @endif
    @include('filament::fields.error-help')
</fieldset>
