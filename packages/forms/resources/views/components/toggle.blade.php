<x-dynamic-component
    :component="$getFieldWrapperView()"
    :id="$getId()"
    :label="$getLabel()"
    :label-sr-only="$isLabelHidden()"
    :helper-text="$getHelperText()"
    :hint="$getHint()"
    :hint-action="$getHintAction()"
    :hint-color="$getHintColor()"
    :hint-icon="$getHintIcon()"
    :required="$isRequired()"
    :state-path="$getStatePath()"
>
    @capture($content)
        <button
            x-data="{
                state: $wire.{{ $applyStateBindingModifiers('entangle(\'' . $getStatePath() . '\')') }},
            }"
            role="switch"
            aria-checked="false"
            x-bind:aria-checked="state?.toString()"
            x-on:click="state = ! state"
            x-bind:class="{
                '{{
                    match ($getOnColor()) {
                        'danger' => 'bg-danger-500',
                        'secondary' => 'bg-gray-500',
                        'success' => 'bg-success-500',
                        'warning' => 'bg-warning-500',
                        default => 'bg-primary-600',
                    }
                }}': state,
                '{{
                    match ($getOffColor()) {
                        'danger' => 'bg-danger-500',
                        'primary' => 'bg-primary-500',
                        'success' => 'bg-success-500',
                        'warning' => 'bg-warning-500',
                        default => 'bg-gray-200',
                    }
                }} @if (config('forms.dark_mode')) dark:bg-white/10 @endif': ! state,
            }"
            {!! $isAutofocused() ? 'autofocus' : null !!}
            {!! $isDisabled() ? 'disabled' : null !!}
            wire:loading.attr="disabled"
            id="{{ $getId() }}"
            dusk="filament.forms.{{ $getStatePath() }}"
            type="button"
            {{
                $attributes
                    ->merge($getExtraAttributes())
                    ->class([
                        'filament-forms-toggle-component relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent outline-none transition-colors duration-200 ease-in-out disabled:pointer-events-none disabled:cursor-not-allowed disabled:opacity-70',
                    ])
            }}
            {{ $getExtraAlpineAttributeBag() }}
        >
            <span
                class="pointer-events-none relative inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                x-bind:class="{
                    'translate-x-5 rtl:-translate-x-5': state,
                    'translate-x-0': ! state,
                }"
            >
                <span
                    class="absolute inset-0 flex h-full w-full items-center justify-center transition-opacity"
                    aria-hidden="true"
                    x-bind:class="{
                        'opacity-0 ease-out duration-100': state,
                        'opacity-100 ease-in duration-200': ! state,
                    }"
                >
                    @if ($hasOffIcon())
                        <x-dynamic-component
                            :component="$getOffIcon()"
                            :class="
                                \Illuminate\Support\Arr::toCssClasses([
                                    'h-3 w-3',
                                    match ($getOffColor()) {
                                        'danger' => 'text-danger-500',
                                        'primary' => 'text-primary-500',
                                        'success' => 'text-success-500',
                                        'warning' => 'text-warning-500',
                                        default => 'text-gray-400',
                                    },
                                ])
                            "
                        />
                    @endif
                </span>

                <span
                    class="absolute inset-0 flex h-full w-full items-center justify-center transition-opacity"
                    aria-hidden="true"
                    x-bind:class="{
                        'opacity-100 ease-in duration-200': state,
                        'opacity-0 ease-out duration-100': ! state,
                    }"
                >
                    @if ($hasOnIcon())
                        <x-dynamic-component
                            :component="$getOnIcon()"
                            x-cloak
                            :class="
                                \Illuminate\Support\Arr::toCssClasses([
                                    'h-3 w-3',
                                    match ($getOnColor()) {
                                        'danger' => 'text-danger-500',
                                        'secondary' => 'text-gray-400',
                                        'success' => 'text-success-500',
                                        'warning' => 'text-warning-500',
                                        default => 'text-primary-500',
                                    },
                                ])
                            "
                        />
                    @endif
                </span>
            </span>
        </button>
    @endcapture

    @if ($isInline())
        <x-slot name="labelPrefix">
            {{ $content() }}
        </x-slot>
    @else
        {{ $content() }}
    @endif
</x-dynamic-component>
