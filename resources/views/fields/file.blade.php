@php
    $disabled = $field->disabled || (!$field->file_multiple && count($this->form_data[$field->name])) >= 1;
    $x_data_function = 'file'.Str::studly($field->name).'()';
@endphp
<fieldset x-data="{{ $x_data_function }}" class="col-span-4 {{ $field->class }}">
    @if ($field->label)
        <legend 
            class="
                label 
                @if ($disabled) 
                    label-disabled
                @endif
                block mb-2
            "
        >
            {{ __($field->label) }}
            @if ($field->required)
                <sup class="text-red-600">*</sup>
                <span class="sr-only">{{ __('required') }}</span>
            @endif
        </legend>
    @endif
    <div class="mb-1 flex items-center">
        <label class="mr-2">
            <input
                id="{{ $field->id }}"
                name="{{ $field->name }}"
                type="file"
                {{ $field->file_multiple ? 'multiple' : '' }}
                @change="add"
                class="sr-only"
                @if ($disabled) 
                    disabled
                @endif
            >
            <span 
                class="btn btn-file-input" 
                :class="{ 'btn-is-disabled': {{ $disabled ? 'true' : 'false' }} }"
                x-text="isLoading() ? '{{ __('Uploading') }}' : '{{ $field->placeholder ?? __('filament::fields/file.upload', ['file' => Str::singular($field->name)]) }}'"
            ></span>
        </label>
        <div class="flex-shrink-0" x-show="isLoading()">
            {{ Filament::svg('puff', 'w-5 h-5') }}
        </div>
    </div>
    @include('filament::fields.error-help')
    @if ($this->form_data[$field->name])
        <ul>
            @foreach ($this->form_data[$field->name] as $key => $value)
                <li class="mt-2 p-2 text-sm leading-5 bg-gray-50 dark:bg-gray-700 rounded overflow-hidden shadow flex items-center justify-between">
                    <a href="{{ Storage::url($value['path']) }}" 
                        target="_blank" 
                        rel="noopener noreferrer"
                        class="flex items-center"
                    >
                        <div class="mr-2">
                            @php($info = $this->fileInfo($value['mime_type']))
                            @if ($info['is_image']) 
                                <img src="{{ Filament::image($value['path'], [
                                    'w' => 40, 
                                    'h' => 40, 
                                    'fit' => 'crop', 
                                    'dpr' => 2
                                ]) }}" 
                                alt="{{ $value['name'] }}" 
                                class="h-10 w-auto rounded" />
                            @else
                                {{ Filament::icon($info['icon'], 'flex-shrink-0 w-10 h-10 text-gray-300') }}
                            @endif
                        </div>
                        <dl>
                            <dt class="sr-only">Name</dt>
                            <dd>{{ $value['name'] }}</dd>
                            <dt class="sr-only">Size</dt>
                            <dd class="text-xs font-mono text-gray-400">{{ Filament::formatBytes($value['size']) }}</dd>
                        </dl>
                    </a>
                    <button type="button"
                        wire:click.prevent="fileRemove('{{ $field->name }}', '{{ $value['path'] }}', {{ $key }})"
                        class="flex-shrink-0 flex items-center p-2"
                    >   
                        <x-heroicon-o-x class="h-4 w-4 text-red-500" />
                        <span class="sr-only">{{ __('filament::fields/file.delete', ['file' => $value['name']]) }}</span>
                    </button>
                </li>
            @endforeach
        </ul>
    @endif
</fieldset>

<script>
    function {{ $x_data_function }} {
        return {
            loading: false,
            isLoading() { 
                return this.loading === true; 
            },
            add(event) {
                let self = this;
                self.loading = true;
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
                    window.livewire.emit('filament.fileUploadError', '{{ $field->name }}', error.statusText);
                }).finally(function() {
                    self.loading = false;
                });                  
            }
        }
    }
</script>