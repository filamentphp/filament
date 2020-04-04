@php($disabled = $field->disabled || (!$field->file_multiple && count($this->form_data[$field->name])) >= 1)
<fieldset x-data="file()" class="col-span-4 {{ $field->class }}">
    @if ($field->label)
        <legend class="block mb-2 text-sm font-medium leading-5 text-gray-700 dark:text-gray-50">
            {{ __($field->label) }}
            @if ($field->required)
                <sup class="text-red-600">*</sup>
                <span class="sr-only">(required)</span>
            @endif
        </legend>
    @endif
    <div class="mb-1 flex items-center">
        <label class="mr-2">
            <input
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
                x-text="isLoading() ? '{{ __('filament::actions.uploading') }}' : '{{ $field->placeholder ?? __('filament::actions.upload', ['item' => Str::singular($field->name)]) }}'"
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
                    <a href="{{ Storage::url($value['file']) }}" 
                        target="_blank" 
                        rel="noopener noreferrer"
                        class="flex items-center"
                    >
                        <div class="mr-2">
                            @php($info = $this->fileInfo($value['mime_type']))
                            @if ($info['is_image']) 
                                <img src="{{ Filament::image($value['file'], [
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
                        wire:click.prevent="fileRemove('{{ $field->name }}', '{{ $value['name'] }}', {{ $key }})"
                        class="flex-shrink-0 flex items-center p-2"
                    >   
                        <x-heroicon-o-x class="h-4 w-4 text-red-500" />
                        <span class="sr-only">{{ __('filament::admin.remove', ['item' => $value['name']]) }}</span>
                    </button>
                </li>
            @endforeach
        </ul>
    @endif
</fieldset>

<script>
    function file() {
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