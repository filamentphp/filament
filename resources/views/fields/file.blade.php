<div class="form-group row">
    <label for="{{ $field->name }}" class="col-md-2 col-form-label text-md-right">
        {{ $field->label }}
    </label>

    <div class="col-md">
        <div class="custom-file">
            <input
                id="{{ $field->name }}"
                type="file"
                class="custom-file-input @error($field->key) is-invalid @enderror"
                {{ $field->file_multiple ? 'multiple' : '' }}>

            <label class="custom-file-label" for="{{ $field->name }}">
                {{ $field->placeholder }}
            </label>
        </div>

        @if ($form_data[$field->name])
            <ul class="list-group mt-2">
                @foreach ($form_data[$field->name] as $key => $value)
                    <li class="list-group-item p-2">
                        <div class="row align-items-center">
                            <div class="col">
                                <a href="{{ Storage::url($value['file']) }}" target="_blank">
                                    <i class="fa fa-fw {{ $this->fileIcon($value['mime_type']) }} mr-1"></i>{{ $value['name'] }}
                                </a>
                            </div>
                            <div class="col-auto">
                                <button class="btn btn-sm btn-danger"
                                        onclick="confirm('{{ __('Are you sure?') }}') || event.stopImmediatePropagation();"
                                        wire:click="arrayRemove('{{ $field->name }}', '{{ $key }}')">
                                    <i class="fa fa-trash-alt"></i>
                                </button>
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif

        @include('filament::fields.error-help')
    </div>
</div>
