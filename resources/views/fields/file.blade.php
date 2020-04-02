<fieldset x-data="file()" class="mb-5">
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
            @change="add"
        >
        @error($field->key) 
            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                <x-heroicon-o-exclamation-circle class="h-5 w-5 text-red-600" />
            </div>
        @enderror
        {{ $field->placeholder }}
    </label>

    @if ($this->form_data[$field->name])
        <ul class="mb-2">
            @foreach ($this->form_data[$field->name] as $key => $value)
                <li class="{{ !$loop->last ? 'mb-2 ' : '' }}text-sm leading-5 bg-gray-50 dark:bg-gray-700 rounded overflow-hidden shadow flex items-center justify-between">
                    <a href="{{ Storage::url($value['file']) }}" 
                        target="_blank" 
                        rel="noopener noreferrer"
                        class="p-2 flex-grow flex items-center mr-2"
                    >
                        {{ Filament::icon($this->fileIcon($value['mime_type']), 'flex-shrink-0 w-5 h-5 mr-2') }}
                        <span class="flex-grow">
                            {{ $value['name'] }}
                        </span>
                    </a>
                    <button type="button"
                        wire:click.prevent="fileRemove('{{ $field->name }}', {{ $key }}, '{{ $value['file'] }}')"
                        class="flex-shrink-0 flex items-center p-2"
                    >   
                        <x-heroicon-o-x class="h-4 w-4 text-red-500" />
                        <span class="sr-only">{{ __('filament::admin.remove', ['item' => $value['name']]) }}</span>
                    </button>
                </li>
            @endforeach
        </ul>
    @endif
    @include('filament::fields.error-help')
</fieldset>

<script>
    function file() {
        return {
            add(event) {
                let form_data = new FormData();
                form_data.append('component', @json(get_class($this)));
                form_data.append('field_name', '{{ $field->name }}');
                form_data.append('validation_rules', '@json($field->file_rules)');
                form_data.append('validation_messages', '@json($field->file_validation_messages)');

                for (let i = 0; i < event.target.files.length; i++) {
                    form_data.append('files[]', event.target.files[i]);
                }

                fetch('{{ route('filament.admin.file-upload') }}', {
                    method: 'POST',
                    body: form_data,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                }).then(function (response) {
                    if (response.ok) {                                
                        return response.json();
                    }
                    return Promise.reject(response);
                }).then(function (data) {
                    if (data.errors) {
                        window.livewire.emit('filament.fileUploadError', data.field_name, data.errors);
                    } else {
                        window.livewire.emit('filament.fileUpdate', data.field_name, data.uploaded_files);
                    }
                }).catch(function (error) {
                    window.livewire.emit('filament.fileUploadError', file.name, error.statusText);
                });                    
            }
        }
    }
</script>