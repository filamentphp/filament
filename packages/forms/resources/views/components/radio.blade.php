<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    @if ($isInline())
        <x-slot name="labelSuffix">
    @endif
            <x-filament::grid
                :default="$getColumns('default')"
                :sm="$getColumns('sm')"
                :md="$getColumns('md')"
                :lg="$getColumns('lg')"
                :xl="$getColumns('xl')"
                :two-xl="$getColumns('2xl')"
                :is-grid="! $isInline()"
                direction="column"
                :attributes="$attributes->merge($getExtraAttributes())->class([
                    'filament-forms-radio-component',
                    'flex flex-wrap gap-3' => $isInline(),
                    'gap-2' => ! $isInline(),
                ])"
            >
                @php
                    $isDisabled = $isDisabled();
                @endphp

                @foreach ($getOptions() as $value => $label)
                    <div @class([
                        'flex items-start',
                        'gap-3' => ! $isInline(),
                        'gap-2' => $isInline(),
                    ])>
                        <div class="flex items-center h-5">
                            <input
                                name="{{ $getId() }}"
                                id="{{ $getId() }}-{{ $value }}"
                                type="radio"
                                value="{{ $value }}"
                                dusk="filament.forms.{{ $getStatePath() }}"
                                {{ $applyStateBindingModifiers('wire:model') }}="{{ $getStatePath() }}"
                                {{ $getExtraInputAttributeBag()->class([
                                    'focus:ring-primary-500 h-4 w-4 text-primary-600 disabled:opacity-70 dark:bg-gray-700 dark:checked:bg-primary-500',
                                    'border-gray-300 dark:border-gray-500' => ! $errors->has($getStatePath()),
                                    'border-danger-600 ring-1 ring-inset ring-danger-600 dark:border-danger-400 dark:ring-danger-400' => $errors->has($getStatePath()),
                                ]) }}
                                {!! ($isDisabled || $isOptionDisabled($value, $label)) ? 'disabled' : null !!}
                                wire:loading.attr="disabled"
                            />
                        </div>

                        <div class="text-sm">
                            <label for="{{ $getId() }}-{{ $value }}" @class([
                                'font-medium',
                                'text-gray-700 dark:text-gray-200' => ! $errors->has($getStatePath()),
                                'text-danger-600 dark:text-danger-400' => $errors->has($getStatePath()),
                            ])>
                                {{ $label }}
                            </label>

                            @if ($hasDescription($value))
                                <p class="text-gray-500 dark:text-gray-400">
                                    {{ $getDescription($value) }}
                                </p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </x-filament::grid>
    @if ($isInline())
        </x-slot>
    @endif
</x-dynamic-component>
