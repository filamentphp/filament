<x-dynamic-component
    :component="$getFieldWrapperView()"
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
        <x-slot name="labelSuffix">
    @endif
            <div {{ $attributes->merge($getExtraAttributes())->class([
                'filament-forms-radio-component',
                'flex flex-wrap gap-3' => $isInline(),
                'grid gap-2' => ! $isInline(),
                'grid-cols-1' => $getColumns('default') === 1,
                'grid-cols-2' => $getColumns('default') === 2,
                'grid-cols-3' => $getColumns('default') === 3,
                'grid-cols-4' => $getColumns('default') === 4,
                'grid-cols-5' => $getColumns('default') === 5,
                'grid-cols-6' => $getColumns('default') === 6,
                'grid-cols-7' => $getColumns('default') === 7,
                'grid-cols-8' => $getColumns('default') === 8,
                'grid-cols-9' => $getColumns('default') === 9,
                'grid-cols-10' => $getColumns('default') === 10,
                'grid-cols-11' => $getColumns('default') === 11,
                'grid-cols-12' => $getColumns('default') === 12,
                'sm:grid-cols-1' => $getColumns('sm') === 1,
                'sm:grid-cols-2' => $getColumns('sm') === 2,
                'sm:grid-cols-3' => $getColumns('sm') === 3,
                'sm:grid-cols-4' => $getColumns('sm') === 4,
                'sm:grid-cols-5' => $getColumns('sm') === 5,
                'sm:grid-cols-6' => $getColumns('sm') === 6,
                'sm:grid-cols-7' => $getColumns('sm') === 7,
                'sm:grid-cols-8' => $getColumns('sm') === 8,
                'sm:grid-cols-9' => $getColumns('sm') === 9,
                'sm:grid-cols-10' => $getColumns('sm') === 10,
                'sm:grid-cols-11' => $getColumns('sm') === 11,
                'sm:grid-cols-12' => $getColumns('sm') === 12,
                'md:grid-cols-1' => $getColumns('md') === 1,
                'md:grid-cols-2' => $getColumns('md') === 2,
                'md:grid-cols-3' => $getColumns('md') === 3,
                'md:grid-cols-4' => $getColumns('md') === 4,
                'md:grid-cols-5' => $getColumns('md') === 5,
                'md:grid-cols-6' => $getColumns('md') === 6,
                'md:grid-cols-7' => $getColumns('md') === 7,
                'md:grid-cols-8' => $getColumns('md') === 8,
                'md:grid-cols-9' => $getColumns('md') === 9,
                'md:grid-cols-10' => $getColumns('md') === 10,
                'md:grid-cols-11' => $getColumns('md') === 11,
                'md:grid-cols-12' => $getColumns('md') === 12,
                'lg:grid-cols-1' => $getColumns('lg') === 1,
                'lg:grid-cols-2' => $getColumns('lg') === 2,
                'lg:grid-cols-3' => $getColumns('lg') === 3,
                'lg:grid-cols-4' => $getColumns('lg') === 4,
                'lg:grid-cols-5' => $getColumns('lg') === 5,
                'lg:grid-cols-6' => $getColumns('lg') === 6,
                'lg:grid-cols-7' => $getColumns('lg') === 7,
                'lg:grid-cols-8' => $getColumns('lg') === 8,
                'lg:grid-cols-9' => $getColumns('lg') === 9,
                'lg:grid-cols-10' => $getColumns('lg') === 10,
                'lg:grid-cols-11' => $getColumns('lg') === 11,
                'lg:grid-cols-12' => $getColumns('lg') === 12,
                'xl:grid-cols-1' => $getColumns('xl') === 1,
                'xl:grid-cols-2' => $getColumns('xl') === 2,
                'xl:grid-cols-3' => $getColumns('xl') === 3,
                'xl:grid-cols-4' => $getColumns('xl') === 4,
                'xl:grid-cols-5' => $getColumns('xl') === 5,
                'xl:grid-cols-6' => $getColumns('xl') === 6,
                'xl:grid-cols-7' => $getColumns('xl') === 7,
                'xl:grid-cols-8' => $getColumns('xl') === 8,
                'xl:grid-cols-9' => $getColumns('xl') === 9,
                'xl:grid-cols-10' => $getColumns('xl') === 10,
                'xl:grid-cols-11' => $getColumns('xl') === 11,
                'xl:grid-cols-12' => $getColumns('xl') === 12,
                '2xl:grid-cols-1' => $getColumns('2xl') === 1,
                '2xl:grid-cols-2' => $getColumns('2xl') === 2,
                '2xl:grid-cols-3' => $getColumns('2xl') === 3,
                '2xl:grid-cols-4' => $getColumns('2xl') === 4,
                '2xl:grid-cols-5' => $getColumns('2xl') === 5,
                '2xl:grid-cols-6' => $getColumns('2xl') === 6,
                '2xl:grid-cols-7' => $getColumns('2xl') === 7,
                '2xl:grid-cols-8' => $getColumns('2xl') === 8,
                '2xl:grid-cols-9' => $getColumns('2xl') === 9,
                '2xl:grid-cols-10' => $getColumns('2xl') === 10,
                '2xl:grid-cols-11' => $getColumns('2xl') === 11,
                '2xl:grid-cols-12' => $getColumns('2xl') === 12,
            ]) }}>
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
                                    'focus:ring-primary-500 h-4 w-4 text-primary-600',
                                    'dark:bg-gray-700 dark:checked:bg-primary-500' => config('forms.dark_mode'),
                                    'border-gray-300' => ! $errors->has($getStatePath()),
                                    'dark:border-gray-500' => (! $errors->has($getStatePath())) && config('forms.dark_mode'),
                                    'border-danger-600 ring-danger-600' => $errors->has($getStatePath()),
                                ]) }}
                                {!! ($isDisabled || $isOptionDisabled($value, $label)) ? 'disabled' : null !!}
                            />
                        </div>

                        <div class="text-sm">
                            <label for="{{ $getId() }}-{{ $value }}" @class([
                                'font-medium',
                                'text-gray-700' => ! $errors->has($getStatePath()),
                                'dark:text-gray-200' => (! $errors->has($getStatePath())) && config('forms.dark_mode'),
                                'text-danger-600' => $errors->has($getStatePath()),
                            ])>
                                {{ $label }}
                            </label>

                            @if ($hasDescription($value))
                                <p @class([
                                    'text-gray-500',
                                    'dark:text-gray-400' => config('forms.dark_mode'),
                                ])>
                                    {{ $getDescription($value) }}
                                </p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
    @if ($isInline())
        </x-slot>
    @endif
</x-dynamic-component>
