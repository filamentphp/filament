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
        <x-filament-support::grid
            :default="$getColumns('default')"
            :sm="$getColumns('sm')"
            :md="$getColumns('md')"
            :lg="$getColumns('lg')"
            :xl="$getColumns('xl')"
            :two-xl="$getColumns('2xl')"
            :is-grid="! $isInline()"
            direction="column"
            :attributes="
                \Filament\Support\prepare_inherited_attributes($attributes->merge($getExtraAttributes())->class([
                    'filament-forms-radio-component flex flex-wrap gap-3',
                    'flex-col' => ! $isInline(),
                ]))
            "
        >
            @php
                $isDisabled = $isDisabled();
            @endphp

            @foreach ($getOptions() as $value => $label)
                @php
                    $shouldOptionBeDisabled = $isDisabled || $isOptionDisabled($value, $label);
                @endphp

                <div
                    @class([
                        'flex items-start',
                        'gap-3' => ! $isInline(),
                        'gap-2' => $isInline(),
                    ])
                >
                    <div class="flex h-5 items-center">
                        <input
                            name="{{ $getId() }}"
                            id="{{ $getId() }}-{{ $value }}"
                            type="radio"
                            value="{{ $value }}"
                            dusk="filament.forms.{{ $getStatePath() }}"
                            {{ $applyStateBindingModifiers('wire:model') }}="{{ $getStatePath() }}"
                            {{
                                $getExtraInputAttributeBag()->class([
                                    'h-4 w-4 text-primary-600 focus:ring-primary-500 disabled:opacity-70',
                                    'dark:bg-gray-700 dark:checked:bg-primary-500' => config('forms.dark_mode'),
                                    'border-gray-300' => ! $errors->has($getStatePath()),
                                    'dark:border-gray-500' => (! $errors->has($getStatePath())) && config('forms.dark_mode'),
                                    'border-danger-600 ring-1 ring-inset ring-danger-600' => $errors->has($getStatePath()),
                                    'dark:border-danger-400 dark:ring-danger-400' => $errors->has($getStatePath()) && config('forms.dark_mode'),
                                ])
                            }}
                            {!! $shouldOptionBeDisabled ? 'disabled' : null !!}
                            wire:loading.attr="disabled"
                        />
                    </div>

                    <div class="text-sm">
                        <label
                            for="{{ $getId() }}-{{ $value }}"
                            @class([
                                'font-medium',
                                'text-gray-700' => ! $errors->has($getStatePath()),
                                'dark:text-gray-200' => (! $errors->has($getStatePath())) && config('forms.dark_mode'),
                                'text-danger-600' => $errors->has($getStatePath()),
                                'dark:text-danger-400' => $errors->has($getStatePath()) && config('forms.dark_mode'),
                                'opacity-50' => $shouldOptionBeDisabled,
                            ])
                        >
                            {{ $label }}
                        </label>

                        @if ($hasDescription($value))
                            <p
                                @class([
                                    'text-gray-500',
                                    'dark:text-gray-400' => config('forms.dark_mode'),
                                ])
                            >
                                {{ $getDescription($value) }}
                            </p>
                        @endif
                    </div>
                </div>
            @endforeach
        </x-filament-support::grid>
    @endcapture

    @if ($isInline())
        <x-slot name="labelSuffix">
            {{ $content() }}
        </x-slot>
    @else
        {{ $content() }}
    @endif
</x-dynamic-component>
