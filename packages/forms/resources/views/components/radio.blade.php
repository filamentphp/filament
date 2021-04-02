<script>
    function radios(config) {
        return {
            name: config.name,
            options: config.initialOptions,
            required: config.required,
            value: config.value,
        }
    }
</script>
<x-forms::field-group :column-span="$formComponent->getColumnSpan()" :error-key="$formComponent->getName()" :for="$formComponent->getId()" :help-message="$formComponent->getHelpMessage()" :hint="$formComponent->getHint()" :label="$formComponent->getLabel()" :required="$formComponent->isRequired()">
    <div x-data="radios({
            initialOptions: {{ json_encode($formComponent->getOptions()) }},
            name: '{{ $formComponent->getName() }}',
            required: {{ $formComponent->isRequired() ? 'true' : 'false' }},
            @if (Str::of($formComponent->getBindingAttribute())->startsWith('wire:model'))
                value: @entangle($formComponent->getName()){{ Str::of($formComponent->getBindingAttribute())->after('wire:model') }},
            @endif
            })" wire:model.defer="{{ $formComponent->getName() }}">
        <template x-for="(key, index) in Object.keys(options)" :key="index">
            <div>
                <input x-bind:id="'{{ $formComponent->getName() }}'+'_'+key" {!! $formComponent->isAutofocused() ? 'autofocus' : null !!}
                {!! $formComponent->isDisabled() ? 'disabled' : null !!}
                {!! $formComponent->getName() ? "{$formComponent->getBindingAttribute()}=\"{$formComponent->getName()}\"" : null !!}
                {!! $formComponent->isRequired() ? 'required' : null !!}
                class="text-primary-600 shadow-sm focus:border-primary-700 focus:ring focus:ring-blue-200 radio focus:ring-opacity-50 {{ $errors->has($formComponent->getName()) ? 'border-danger-600 ' : 'border-gray-300' }}"
                {!! Filament\format_attributes($formComponent->getExtraAttributes()) !!}
                type="radio"
                name="{{$formComponent->getName()}}"
                x-bind:value="key"
                x-bind:checked="key === value"
                />
                <label x-bind:for="'{{ $formComponent->getName() }}'+'_'+key" x-text="options[key]"></label>
            </div>
        </template>
    </div>

</x-forms::field-group>