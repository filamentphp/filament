@php
    $datalistOptions = $getDatalistOptions();
    $id = $getId();
    $isDisabled = $isDisabled();
    $statePath = $getStatePath();
    $prefixIcon = $getPrefixIcon();
    $prefixLabel = $getPrefixLabel();
    $suffixIcon = $getSuffixIcon();
    $suffixLabel = $getSuffixLabel();
@endphp

<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    <x-filament::input.affixes
        :state-path="$statePath"
        :prefix="$prefixLabel"
        :prefix-actions="$getPrefixActions()"
        :prefix-icon="$prefixIcon"
        :suffix="$suffixLabel"
        :suffix-actions="$getSuffixActions()"
        :suffix-icon="$suffixIcon"
        class="filament-forms-text-input-component"
        :attributes="$getExtraAttributeBag()"
    >
        @if ($isNative())
            <input
                x-data="{}"
                x-bind:class="{
                    'border-gray-300 dark:border-gray-600': ! (@js($statePath) in $wire.__instance.serverMemo.errors),
                    'border-danger-600 ring-danger-600': (@js($statePath) in $wire.__instance.serverMemo.errors),
                }"
                {{
                    $getExtraInputAttributeBag()
                        ->merge([
                            'autofocus' => $isAutofocused(),
                            'disabled' => $isDisabled,
                            'dusk' => "filament.forms.{$statePath}",
                            'id' => $id,
                            'list' => $datalistOptions ? "{$id}-list" : null,
                            'max' => (! $isConcealed) ? $getMaxDate() : null,
                            'min' => (! $isConcealed) ? $getMinDate() : null,
                            'placeholder' => $getPlaceholder(),
                            'readonly' => $isReadOnly(),
                            'required' => $isRequired() && (! $isConcealed),
                            'step' => $getStep(),
                            'type' => $getType(),
                            $applyStateBindingModifiers('wire:model') => $statePath,
                        ], escape: false)
                        ->class([
                            'block w-full transition duration-75 shadow-sm outline-none sm:text-sm focus:border-primary-500 focus:relative focus:z-[1] focus:ring-1 focus:ring-inset focus:ring-primary-500 disabled:opacity-70 dark:bg-gray-700 dark:text-white dark:focus:border-primary-500',
                            'rounded-l-lg' => ! ($prefixLabel || $prefixIcon),
                            'rounded-r-lg' => ! ($suffixLabel || $suffixIcon),
                        ])
                }}
            />
        @else
            <div
                x-ignore
                ax-load
                ax-load-src="{{ \Filament\Support\Facades\FilamentAsset::getAlpineComponentSrc('date-time-picker', 'filament/forms') }}"
                x-data="dateTimePickerFormComponent({
                    displayFormat: '{{ convert_date_format($getDisplayFormat())->to('day.js') }}',
                    firstDayOfWeek: {{ $getFirstDayOfWeek() }},
                    isAutofocused: @js($isAutofocused()),
                    locale: @js(app()->getLocale()),
                    shouldCloseOnDateSelection: @js($shouldCloseOnDateSelection()),
                    state: $wire.{{ $applyStateBindingModifiers('entangle(\'' . $statePath . '\')') }},
                })"
                x-on:keydown.esc="isOpen() && $event.stopPropagation()"
                {{
                    $attributes
                        ->merge($getExtraAttributes(), escape: false)
                        ->merge($getExtraAlpineAttributes(), escape: false)
                        ->class(['filament-forms-date-time-picker-component relative'])
                }}
            >
                <input x-ref="maxDate" type="hidden" value="{{ $getMaxDate() }}" />
                <input x-ref="minDate" type="hidden" value="{{ $getMinDate() }}" />
                <input x-ref="disabledDates" type="hidden" value="{{ json_encode($getDisabledDates()) }}" />

                <button
                    x-ref="button"
                    x-on:click="togglePanelVisibility()"
                    x-on:keydown.enter.stop.prevent="if (! $el.disabled) { isOpen() ? selectDate() : togglePanelVisibility() }"
                    x-on:keydown.arrow-left.stop.prevent="if (! $el.disabled) focusPreviousDay()"
                    x-on:keydown.arrow-right.stop.prevent="if (! $el.disabled) focusNextDay()"
                    x-on:keydown.arrow-up.stop.prevent="if (! $el.disabled) focusPreviousWeek()"
                    x-on:keydown.arrow-down.stop.prevent="if (! $el.disabled) focusNextWeek()"
                    x-on:keydown.backspace.stop.prevent="if (! $el.disabled) clearState()"
                    x-on:keydown.clear.stop.prevent="if (! $el.disabled) clearState()"
                    x-on:keydown.delete.stop.prevent="if (! $el.disabled) clearState()"
                    aria-label="{{ $getPlaceholder() }}"
                    dusk="filament.forms.{{ $statePath }}.open"
                    type="button"
                    tabindex="-1"
                    @disabled($isDisabled)
                    {{ $getExtraTriggerAttributeBag()->class([
                        'bg-white relative w-full border py-2 pl-3 pr-10 rtl:pl-10 rtl:pr-3 text-start cursor-default shadow-sm outline-none sm:text-sm dark:bg-gray-700',
                        'focus-within:ring-1 focus-within:border-primary-500 focus-within:ring-inset focus-within:ring-primary-500' => ! $isDisabled,
                        'border-gray-300 dark:border-gray-600' => ! $errors->has($statePath),
                        'border-danger-600 dark:border-danger-400' => $errors->has($statePath),
                        'opacity-70 dark:text-gray-300' => $isDisabled,
                        'rounded-l-lg' => ! ($prefixLabel || $prefixIcon),
                        'rounded-r-lg' => ! ($suffixLabel || $suffixIcon),
                    ]) }}
                >
                    <input
                        readonly
                        placeholder="{{ $getPlaceholder() }}"
                        wire:key="{{ $this->id }}.{{ $statePath }}.{{ $field::class }}.display-text"
                        x-model="displayText"
                        @if ($id = $getId()) id="{{ $id }}" @endif
                        @class([
                            'w-full h-full p-0 placeholder-gray-400 bg-transparent border-0 outline-none focus:placeholder-gray-500 focus:ring-0 dark:bg-gray-700 dark:placeholder-gray-400',
                            'cursor-default' => $isDisabled,
                        ])
                    />

                    <span class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none rtl:right-auto rtl:left-0 rtl:pl-2">
                        <x-filament::icon
                            name="heroicon-m-calendar"
                            alias="filament-forms::components.date-time-picker.suffix"
                            color="text-gray-400 dark:text-gray-400"
                            size="h-5 w-5"
                        />
                    </span>
                </button>

                <div
                    x-ref="panel"
                    x-cloak
                    x-float.placement.bottom-start.offset.flip.shift="{ offset: 8 }"
                    wire:ignore
                    wire:key="{{ $this->id }}.{{ $statePath }}.{{ $field::class }}.panel"
                    @class([
                        'absolute hidden z-10 my-1 bg-white border border-gray-300 rounded-lg shadow-md dark:bg-gray-700 dark:border-gray-600',
                        'p-4 min-w-[16rem] w-fit' => $hasDate(),
                    ])
                >
                    <div class="space-y-3">
                        @if ($hasDate())
                            <div class="flex items-center justify-between space-x-1 rtl:space-x-reverse">
                                <select
                                    x-model="focusedMonth"
                                    class="grow px-1 py-0 text-lg font-medium text-gray-800 border-0 cursor-pointer outline-none focus:ring-0 dark:bg-gray-700 dark:text-gray-200"
                                    dusk="filament.forms.{{ $statePath }}.focusedMonth"
                                >
                                    <template x-for="(month, index) in months">
                                        <option x-bind:value="index" x-text="month"></option>
                                    </template>
                                </select>

                                <input
                                    type="number"
                                    inputmode="numeric"
                                    x-model.debounce="focusedYear"
                                    class="w-20 p-0 text-lg text-end border-0 outline-none focus:ring-0 dark:bg-gray-700 dark:text-gray-200"
                                    dusk="filament.forms.{{ $statePath }}.focusedYear"
                                />
                            </div>

                            <div class="grid grid-cols-7 gap-1">
                                <template x-for="(day, index) in dayLabels" x-bind:key="index">
                                    <div
                                        x-text="day"
                                        class="text-xs font-medium text-center text-gray-800 dark:text-gray-200"
                                    ></div>
                                </template>
                            </div>

                            <div role="grid" class="grid grid-cols-7 gap-1">
                                <template x-for="day in emptyDaysInFocusedMonth" x-bind:key="day">
                                    <div class="text-sm text-center border border-transparent"></div>
                                </template>

                                <template x-for="day in daysInFocusedMonth" x-bind:key="day">
                                    <div
                                        x-text="day"
                                        x-on:click="dayIsDisabled(day) || selectDate(day)"
                                        x-on:mouseenter="setFocusedDay(day)"
                                        role="option"
                                        x-bind:aria-selected="focusedDate.date() === day"
                                        x-bind:class="{
                                            'text-gray-700 dark:text-gray-300': ! dayIsSelected(day),
                                            'cursor-pointer': ! dayIsDisabled(day),
                                            'bg-primary-50 dark:bg-primary-100 dark:text-gray-600': dayIsToday(day) && ! dayIsSelected(day) && focusedDate.date() !== day && ! dayIsDisabled(day),
                                            'bg-primary-200 dark:text-gray-600': focusedDate.date() === day && ! dayIsSelected(day),
                                            'bg-primary-500 text-white': dayIsSelected(day),
                                            'pointer-events-none': dayIsDisabled(day),
                                            'opacity-50': focusedDate.date() !== day && dayIsDisabled(day),
                                        }"
                                        x-bind:dusk="'filament.forms.{{ $statePath }}' + '.focusedDate.' + day"
                                        class="text-sm leading-loose text-center transition duration-100 ease-in-out rounded-full"
                                    ></div>
                                </template>
                            </div>
                        @endif

                        @if ($hasTime())
                            <div class="flex items-center justify-center bg-gray-50 py-2 rounded-lg rtl:flex-row-reverse dark:bg-gray-800">
                                <input
                                    max="23"
                                    min="0"
                                    step="{{ $getHoursStep() }}"
                                    type="number"
                                    inputmode="numeric"
                                    x-model.debounce="hour"
                                    class="w-16 p-0 pr-1 text-xl bg-gray-50 text-center text-gray-700 border-0 outline-none focus:ring-0 dark:text-gray-200 dark:bg-gray-800"
                                    dusk="filament.forms.{{ $statePath }}.hour"
                                />

                                <span class="text-xl font-medium bg-gray-50 text-gray-700 dark:text-gray-200 dark:bg-gray-800">:</span>

                                <input
                                    max="59"
                                    min="0"
                                    step="{{ $getMinutesStep() }}"
                                    type="number"
                                    inputmode="numeric"
                                    x-model.debounce="minute"
                                    class="w-16 p-0 pr-1 text-xl text-center bg-gray-50 text-gray-700 border-0 outline-none focus:ring-0 dark:text-gray-200 dark:bg-gray-800"
                                    dusk="filament.forms.{{ $statePath }}.minute"
                                />

                                @if ($hasSeconds())
                                    <span class="text-xl font-medium text-gray-700 bg-gray-50 dark:text-gray-200 dark:bg-gray-800">:</span>

                                    <input
                                        max="59"
                                        min="0"
                                        step="{{ $getSecondsStep() }}"
                                        type="number"
                                        inputmode="numeric"
                                        x-model.debounce="second"
                                        dusk="filament.forms.{{ $statePath }}.second"
                                        class="w-16 p-0 pr-1 text-xl text-center bg-gray-50 text-gray-700 border-0 outline-none focus:ring-0 dark:text-gray-200 dark:bg-gray-800"
                                    />
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </x-filament::input.affixes>

    @if ($datalistOptions)
        <datalist id="{{ $id }}-list">
            @foreach ($datalistOptions as $option)
                <option value="{{ $option }}" />
            @endforeach
        </datalist>
    @endif
</x-dynamic-component>
