<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    @php
        $id = $getId();
        $isDisabled = $isDisabled();
        $isInline = $isInline();
        $statePath = $getStatePath();
    @endphp

    @if ($isInline)
        <x-slot name="labelSuffix">
    @endif
            <x-filament::grid
                :default="$getColumns('default')"
                :sm="$getColumns('sm')"
                :md="$getColumns('md')"
                :lg="$getColumns('lg')"
                :xl="$getColumns('xl')"
                :two-xl="$getColumns('2xl')"
                :is-grid="! $isInline"
                direction="column"
                :attributes="$attributes->merge($getExtraAttributes(), escape: false)->class([
                    'filament-forms-radio-component flex flex-wrap gap-3',
                    'flex-col' => ! $isInline,
                ])"
            >
                @foreach ($getOptions() as $value => $label)
                    <div @class([
                        'flex items-start',
                        'gap-3' => ! $isInline,
                        'gap-2' => $isInline,
                    ])>
                        <div class="flex items-center h-5">
                            <input
                                name="{{ $id }}"
                                id="{{ $id }}-{{ $value }}"
                                type="radio"
                                value="{{ $value }}"
                                dusk="filament.forms.{{ $statePath }}"
                                {{ $applyStateBindingModifiers('wire:model') }}="{{ $statePath }}"
                                {{ $getExtraInputAttributeBag()->class([
                                    'focus:ring-primary-500 h-4 w-4 text-primary-600 outline-none disabled:opacity-70 dark:bg-gray-700 dark:checked:bg-primary-500',
                                    'border-gray-300 dark:border-gray-500' => ! $errors->has($statePath),
                                    'border-danger-600 ring-1 ring-inset ring-danger-600 dark:border-danger-400 dark:ring-danger-400' => $errors->has($statePath),
                                ]) }}
                                @disabled($isDisabled || $isOptionDisabled($value, $label))
                                wire:loading.attr="disabled"
                            />
                        </div>

                        <div class="text-sm">
                            <label for="{{ $id }}-{{ $value }}" @class([
                                'font-medium',
                                'text-gray-700 dark:text-gray-200' => ! $errors->has($statePath),
                                'text-danger-600 dark:text-danger-400' => $errors->has($statePath),
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
    @if ($isInline)
        </x-slot>
    @endif
</x-dynamic-component>
