@php
    $datalistOptions = $getDatalistOptions();
    $icon = $getIcon();
    $id = $getId();
    $isDisabled = $isDisabled();
    $statePath = $getStatePath();
    $prefixIcon = $getPrefixIcon();
    $prefixLabel = $getPrefixLabel();
    $suffixIcon = $getSuffixIcon();
    $suffixLabel = $getSuffixLabel();
@endphp

<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    <x-filament-forms::affixes
        :state-path="$statePath"
        :prefix="$prefixLabel"
        :prefix-actions="$getPrefixActions()"
        :prefix-icon="$prefixIcon"
        :suffix="$suffixLabel"
        :suffix-actions="$getSuffixActions()"
        :suffix-icon="$suffixIcon"
        class="filament-forms-text-input-component"
        :attributes="\Filament\Support\prepare_inherited_attributes($getExtraAttributeBag())"
    >
        @if ($isNative())
            <input
                x-data="{}"
                x-bind:class="{
                    'border-gray-300 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:focus:border-primary-500': ! (@js($statePath) in $wire.__instance.serverMemo.errors),
                    'border-danger-600 ring-danger-600 dark:border-danger-400 dark:ring-danger-400': (@js($statePath) in $wire.__instance.serverMemo.errors),
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
                            'block w-full shadow-sm outline-none transition duration-75 focus:relative focus:z-[1] focus:ring-1 focus:ring-inset disabled:opacity-70 dark:bg-gray-700 dark:text-white sm:text-sm',
                            'rounded-s-lg' => ! ($prefixLabel || $prefixIcon),
                            'rounded-e-lg' => ! ($suffixLabel || $suffixIcon),
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
                    state: $wire.{{ $applyStateBindingModifiers("entangle('{$statePath}')") }},
                })"
                x-on:keydown.esc="isOpen() && $event.stopPropagation()"
                {{
                    $attributes
                        ->merge($getExtraAttributes(), escape: false)
                        ->merge($getExtraAlpineAttributes(), escape: false)
                        ->class(['filament-forms-date-time-picker-component relative'])
                }}
            >
                <input
                    x-ref="maxDate"
                    type="hidden"
                    value="{{ $getMaxDate() }}"
                />
                <input
                    x-ref="minDate"
                    type="hidden"
                    value="{{ $getMinDate() }}"
                />
                <input
                    x-ref="disabledDates"
                    type="hidden"
                    value="{{ json_encode($getDisabledDates()) }}"
                />

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
                    {{
                        $getExtraTriggerAttributeBag()->class([
                            'relative w-full cursor-default border bg-white py-2 text-start shadow-sm outline-none dark:bg-gray-700 sm:text-sm',
                            'focus-within:ring-1 focus-within:ring-inset' => ! $isDisabled,
                            'border-gray-300 focus-within:border-primary-500 focus-within:ring-primary-500 dark:border-gray-600 dark:focus-within:border-primary-500' => ! $errors->has($statePath),
                            'border-danger-600 ring-danger-600 dark:border-danger-400 dark:ring-danger-400' => $errors->has($statePath),
                            'opacity-70 dark:text-gray-300' => $isDisabled,
                            'rounded-s-lg' => ! ($prefixLabel || $prefixIcon),
                            'rounded-e-lg' => ! ($suffixLabel || $suffixIcon),
                            'px-3' => $icon === false,
                            'pe-10 ps-3' => $icon !== false,
                        ])
                    }}
                >
                    <input
                        readonly
                        placeholder="{{ $getPlaceholder() }}"
                        wire:key="{{ $this->id }}.{{ $statePath }}.{{ $field::class }}.display-text"
                        x-model="displayText"
                        @if ($id = $getId()) id="{{ $id }}" @endif
                        @class([
                            'h-full w-full border-0 bg-transparent p-0 placeholder-gray-400 outline-none focus:placeholder-gray-500 focus:outline-none focus:ring-0 dark:bg-gray-700 dark:placeholder-gray-400',
                            'cursor-default' => $isDisabled,
                        ])
                    />

                    @if ($icon !== false)
                        <span
                            class="pointer-events-none absolute inset-y-0 end-0 flex items-center pe-2"
                        >
                            <x-filament::icon
                                :name="$icon"
                                alias="forms::components.date-time-picker.suffix"
                                color="text-gray-400 dark:text-gray-400"
                                size="h-5 w-5"
                            />
                        </span>
                    @endif
                </button>

                <div
                    x-ref="panel"
                    x-cloak
                    x-float.placement.bottom-start.offset.flip.shift="{ offset: 8 }"
                    wire:ignore
                    wire:key="{{ $this->id }}.{{ $statePath }}.{{ $field::class }}.panel"
                    @class([
                        'absolute z-10 my-1 hidden rounded-lg border border-gray-300 bg-white shadow-md dark:border-gray-600 dark:bg-gray-700',
                        'w-fit min-w-[16rem] p-4' => $hasDate(),
                    ])
                >
                    <div class="space-y-3">
                        @if ($hasDate())
                            <div
                                class="flex items-center justify-between space-x-1 rtl:space-x-reverse"
                            >
                                <select
                                    x-model="focusedMonth"
                                    class="grow cursor-pointer border-0 px-1 py-0 text-lg font-medium text-gray-800 outline-none focus:ring-0 dark:bg-gray-700 dark:text-gray-200"
                                    dusk="filament.forms.{{ $statePath }}.focusedMonth"
                                >
                                    <template x-for="(month, index) in months">
                                        <option
                                            x-bind:value="index"
                                            x-text="month"
                                        ></option>
                                    </template>
                                </select>

                                <input
                                    type="number"
                                    inputmode="numeric"
                                    x-model.debounce="focusedYear"
                                    class="w-20 border-0 p-0 text-end text-lg outline-none focus:ring-0 dark:bg-gray-700 dark:text-gray-200"
                                    dusk="filament.forms.{{ $statePath }}.focusedYear"
                                />
                            </div>

                            <div class="grid grid-cols-7 gap-1">
                                <template
                                    x-for="(day, index) in dayLabels"
                                    x-bind:key="index"
                                >
                                    <div
                                        x-text="day"
                                        class="text-center text-xs font-medium text-gray-800 dark:text-gray-200"
                                    ></div>
                                </template>
                            </div>

                            <div role="grid" class="grid grid-cols-7 gap-1">
                                <template
                                    x-for="day in emptyDaysInFocusedMonth"
                                    x-bind:key="day"
                                >
                                    <div
                                        class="border border-transparent text-center text-sm"
                                    ></div>
                                </template>

                                <template
                                    x-for="day in daysInFocusedMonth"
                                    x-bind:key="day"
                                >
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
                                        class="rounded-full text-center text-sm leading-loose transition duration-100 ease-in-out"
                                    ></div>
                                </template>
                            </div>
                        @endif

                        @if ($hasTime())
                            <div
                                class="flex items-center justify-center rounded-lg bg-gray-50 py-2 rtl:flex-row-reverse dark:bg-gray-800"
                            >
                                <input
                                    max="23"
                                    min="0"
                                    step="{{ $getHoursStep() }}"
                                    type="number"
                                    inputmode="numeric"
                                    x-model.debounce="hour"
                                    class="w-16 border-0 bg-gray-50 p-0 pe-1 text-center text-xl text-gray-700 outline-none focus:ring-0 dark:bg-gray-800 dark:text-gray-200"
                                    dusk="filament.forms.{{ $statePath }}.hour"
                                />

                                <span
                                    class="bg-gray-50 text-xl font-medium text-gray-700 dark:bg-gray-800 dark:text-gray-200"
                                >
                                    :
                                </span>

                                <input
                                    max="59"
                                    min="0"
                                    step="{{ $getMinutesStep() }}"
                                    type="number"
                                    inputmode="numeric"
                                    x-model.debounce="minute"
                                    class="w-16 border-0 bg-gray-50 p-0 pe-1 text-center text-xl text-gray-700 outline-none focus:ring-0 dark:bg-gray-800 dark:text-gray-200"
                                    dusk="filament.forms.{{ $statePath }}.minute"
                                />

                                @if ($hasSeconds())
                                    <span
                                        class="bg-gray-50 text-xl font-medium text-gray-700 dark:bg-gray-800 dark:text-gray-200"
                                    >
                                        :
                                    </span>

                                    <input
                                        max="59"
                                        min="0"
                                        step="{{ $getSecondsStep() }}"
                                        type="number"
                                        inputmode="numeric"
                                        x-model.debounce="second"
                                        dusk="filament.forms.{{ $statePath }}.second"
                                        class="w-16 border-0 bg-gray-50 p-0 pe-1 text-center text-xl text-gray-700 outline-none focus:ring-0 dark:bg-gray-800 dark:text-gray-200"
                                    />
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </x-filament-forms::affixes>

    @if ($datalistOptions)
        <datalist id="{{ $id }}-list">
            @foreach ($datalistOptions as $option)
                <option value="{{ $option }}" />
            @endforeach
        </datalist>
    @endif
</x-dynamic-component>
