<x-forms::field-wrapper
    :id="$getId()"
    :label="$getLabel()"
    :label-sr-only="$isLabelHidden()"
    :helper-text="$getHelperText()"
    :hint="$getHint()"
    :hint-icon="$getHintIcon()"
    :required="$isRequired()"
    :state-path="$getStatePath()"
>
    @if ($isInline())
        <x-slot name="labelPrefix">
    @endif
            <button
                x-data="{ state: $wire.{{ $applyStateBindingModifiers('entangle(\'' . $getStatePath() . '\')') }} }"
                role="switch"
                aria-checked="false"
                x-bind:aria-checked="state.toString()"
                x-on:click="state = ! state"
                x-bind:class="{
                    'bg-primary-600': state,
                    'bg-gray-200': ! state,
                }"
                x-cloak
                {!! $isAutofocused() ? 'autofocus' : null !!}
                {!! $isDisabled() ? 'disabled' : null !!}
                id="{{ $getId() }}"
                type="button"
                {{ $attributes->merge($getExtraAttributes())->class([
                    'relative inline-flex shrink-0 h-6 w-11 border-2 border-transparent rounded-full cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-500 bg-gray-200',
                    'border-gray-300' => ! $errors->has($getStatePath()),
                    'border-danger-300 ring-danger-500' => $errors->has($getStatePath()),
                    'filament-forms-toggle-component',
                ]) }}
                {{ $getExtraAlpineAttributeBag() }}
            >
                <span
                    class="pointer-events-none relative inline-block h-5 w-5 rounded-full bg-white shadow transform ring-0 ease-in-out transition duration-200 translate-x-0"
                    :class="{
                        'translate-x-5 rtl:-translate-x-5': state,
                        'translate-x-0': ! state,
                    }"
                >
                    <span
                        class="absolute inset-0 h-full w-full flex items-center justify-center transition-opacity opacity-100 ease-in duration-200"
                        aria-hidden="true"
                        :class="{
                            'opacity-0 ease-out duration-100': state,
                            'opacity-100 ease-in duration-200': ! state,
                        }"
                    >
                        @if ($hasOffIcon())
                            <x-dynamic-component :component="$getOffIcon()" class="bg-white h-3 w-3 text-gray-400" />
                        @endif
                    </span>

                    <span
                        class="absolute inset-0 h-full w-full flex items-center justify-center transition-opacity opacity-0 ease-out duration-100"
                        aria-hidden="true"
                        :class="{
                            'opacity-100 ease-in duration-200': state,
                            'opacity-0 ease-out duration-100': ! state,
                        }"
                    >
                        @if ($hasOnIcon())
                            <x-dynamic-component :component="$getOnIcon()" class="bg-white h-3 w-3 text-primary-600" />
                        @endif
                    </span>
                </span>
            </button>
    @if ($isInline())
        </x-slot>
    @endif
</x-forms::field-wrapper>
