<x-forms::field-group
    :column-span="$formComponent->getColumnSpan()"
    :error-key="$formComponent->getName()"
    :for="$formComponent->getId()"
    :help-message="$formComponent->getHelpMessage()"
    :hint="$formComponent->getHint()"
    :label="$formComponent->getLabel()"
    :required="$formComponent->isRequired()"
>
    @if($formComponent->isInlineLayout())
    <x-slot name="labelPrefix">
    @endif
        <button
            x-data="{ value: @entangle($formComponent->getName()){{ Str::of($formComponent->getBindingAttribute())->after('wire:model') }} }"
            role="switch"
            aria-checked="false"
            x-bind:aria-checked="value.toString()"
            x-on:click="value = ! value"
            x-bind:class="{
                'bg-primary-600': value,
                'bg-gray-200': ! value,
            }"
            {!! $formComponent->getId() ? "id=\"{$formComponent->getId()}\"" : null !!}
            {!! $formComponent->isAutofocused() ? 'autofocus' : null !!}
            {!! $formComponent->isDisabled() ? 'disabled' : null !!}
            type="button"
            class="relative inline-flex flex-shrink-0 h-6 w-11 border-2 border-transparent rounded-full cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 bg-gray-200"
            {!! Filament\format_attributes($formComponent->getExtraAttributes()) !!}
        >
            <span
                class="pointer-events-none relative inline-block h-5 w-5 rounded-full bg-white shadow transform ring-0 transition ease-in-out duration-200 translate-x-0"
                :class="{
                    'translate-x-5': value,
                    'translate-x-0': ! value,
                }"
            >
                <span
                    class="absolute inset-0 h-full w-full flex items-center justify-center transition-opacity opacity-100 ease-in duration-200"
                    aria-hidden="true"
                    :class="{
                        'opacity-0 ease-out duration-100': value,
                        'opacity-100 ease-in duration-200': ! value,
                    }"
                >
                    @if ($formComponent->hasOffIcon())
                        <x-dynamic-component :component="$formComponent->getOffIcon()" class="bg-white h-3 w-3 text-gray-400" />
                    @endif
                </span>

                <span
                    class="absolute inset-0 h-full w-full flex items-center justify-center transition-opacity opacity-0 ease-out duration-100"
                    aria-hidden="true"
                    :class="{
                        'opacity-100 ease-in duration-200': value,
                        'opacity-0 ease-out duration-100': ! value,
                    }"
                >
                    @if ($formComponent->hasOnIcon())
                        <x-dynamic-component :component="$formComponent->getOnIcon()" class="bg-white h-3 w-3 text-primary-600" />
                    @endif
                </span>
            </span>
        </button>
    @if($formComponent->isInlineLayout())
    </x-slot>
    @endif
</x-forms::field-group>
