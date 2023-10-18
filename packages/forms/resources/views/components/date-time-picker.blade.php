@php
    $datalistOptions = $getDatalistOptions();
    $extraAlpineAttributes = $getExtraAlpineAttributes();
    $id = $getId();
    $isDisabled = $isDisabled();
    $isPrefixInline = $isPrefixInline();
    $isSuffixInline = $isSuffixInline();
    $prefixActions = $getPrefixActions();
    $prefixIcon = $getPrefixIcon();
    $prefixLabel = $getPrefixLabel();
    $suffixActions = $getSuffixActions();
    $suffixIcon = $getSuffixIcon();
    $suffixLabel = $getSuffixLabel();
    $statePath = $getStatePath();
@endphp

<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    <x-filament::input.wrapper
        :disabled="$isDisabled"
        :inline-prefix="$isPrefixInline"
        :inline-suffix="$isSuffixInline"
        :prefix="$prefixLabel"
        :prefix-actions="$prefixActions"
        :prefix-icon="$prefixIcon"
        :prefix-icon-color="$getPrefixIconColor()"
        :suffix="$suffixLabel"
        :suffix-actions="$suffixActions"
        :suffix-icon="$suffixIcon"
        :suffix-icon-color="$getSuffixIconColor()"
        :valid="! $errors->has($statePath)"
        :attributes="\Filament\Support\prepare_inherited_attributes($getExtraAttributeBag())"
    >
        @if ($isNative())
            <x-filament::input
                :attributes="
                    \Filament\Support\prepare_inherited_attributes($getExtraInputAttributeBag())
                        ->merge($extraAlpineAttributes, escape: false)
                        ->merge([
                            'autofocus' => $isAutofocused(),
                            'disabled' => $isDisabled,
                            'id' => $id,
                            'inlinePrefix' => $isPrefixInline && (count($prefixActions) || $prefixIcon || filled($prefixLabel)),
                            'inlineSuffix' => $isSuffixInline && (count($suffixActions) || $suffixIcon || filled($suffixLabel)),
                            'list' => $datalistOptions ? $id . '-list' : null,
                            'max' => (! $isConcealed) ? $getMaxDate() : null,
                            'min' => (! $isConcealed) ? $getMinDate() : null,
                            'placeholder' => $getPlaceholder(),
                            'readonly' => $isReadOnly(),
                            'required' => $isRequired() && (! $isConcealed),
                            'step' => $getStep(),
                            'type' => $getType(),
                            $applyStateBindingModifiers('wire:model') => $statePath,
                            'x-data' => count($extraAlpineAttributes) ? '{}' : null,
                        ], escape: false)
                "
            />
        @else
            <div
                x-ignore
                ax-load
                ax-load-src="{{ \Filament\Support\Facades\FilamentAsset::getAlpineComponentSrc('date-time-picker', 'filament/forms') }}"
                x-data="dateTimePickerFormComponent({
                            displayFormat:
                                '{{ convert_date_format($getDisplayFormat())->to('day.js') }}',
                            firstDayOfWeek: {{ $getFirstDayOfWeek() }},
                            isAutofocused: @js($isAutofocused()),
                            locale: @js(app()->getLocale()),
                            shouldCloseOnDateSelection: @js($shouldCloseOnDateSelection()),
                            state: $wire.{{ $applyStateBindingModifiers("\$entangle('{$statePath}')") }},
                        })"
                x-on:keydown.esc="isOpen() && $event.stopPropagation()"
                {{
                    $attributes
                        ->merge($getExtraAttributes(), escape: false)
                        ->merge($getExtraAlpineAttributes(), escape: false)
                        ->class(['fi-fo-date-time-picker'])
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
                    x-on:keydown.enter.stop.prevent="
                        if (! $el.disabled) {
                            isOpen() ? selectDate() : togglePanelVisibility()
                        }
                    "
                    x-on:keydown.arrow-left.stop.prevent="if (! $el.disabled) focusPreviousDay()"
                    x-on:keydown.arrow-right.stop.prevent="if (! $el.disabled) focusNextDay()"
                    x-on:keydown.arrow-up.stop.prevent="if (! $el.disabled) focusPreviousWeek()"
                    x-on:keydown.arrow-down.stop.prevent="if (! $el.disabled) focusNextWeek()"
                    x-on:keydown.backspace.stop.prevent="if (! $el.disabled) clearState()"
                    x-on:keydown.clear.stop.prevent="if (! $el.disabled) clearState()"
                    x-on:keydown.delete.stop.prevent="if (! $el.disabled) clearState()"
                    aria-label="{{ $getPlaceholder() }}"
                    type="button"
                    tabindex="-1"
                    @disabled($isDisabled)
                    {{
                        $getExtraTriggerAttributeBag()->class([
                            'w-full',
                        ])
                    }}
                >
                    <input
                        @disabled($isDisabled)
                        readonly
                        placeholder="{{ $getPlaceholder() }}"
                        wire:key="{{ $this->getId() }}.{{ $statePath }}.{{ $field::class }}.display-text"
                        x-model="displayText"
                        @if ($id = $getId()) id="{{ $id }}" @endif
                        @class([
                            'w-full border-none bg-transparent px-3 py-1.5 text-base text-gray-950 outline-none transition duration-75 placeholder:text-gray-400 focus-visible:ring-0 disabled:text-gray-500 disabled:[-webkit-text-fill-color:theme(colors.gray.500)] dark:text-white dark:placeholder:text-gray-500 dark:disabled:text-gray-400 dark:disabled:[-webkit-text-fill-color:theme(colors.gray.400)] sm:text-sm sm:leading-6',
                        ])
                    />
                </button>

                <div
                    x-ref="panel"
                    x-cloak
                    x-float.placement.bottom-start.offset.flip.shift="{ offset: 8 }"
                    wire:ignore
                    wire:key="{{ $this->getId() }}.{{ $statePath }}.{{ $field::class }}.panel"
                    @class([
                        'fi-fo-date-time-picker-panel absolute z-10 rounded-lg bg-white p-4 shadow-lg ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10',
                    ])
                >
                    <div class="grid gap-y-3">
                        @if ($hasDate())
                            <div class="flex items-center justify-between">
                                <select
                                    x-model="focusedMonth"
                                    class="grow cursor-pointer border-none bg-transparent p-0 text-sm font-medium text-gray-950 focus-visible:ring-0 dark:bg-gray-900 dark:text-white"
                                >
                                    <template
                                        x-for="(month, index) in months"
                                    >
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
                                    class="w-16 border-none bg-transparent p-0 text-right text-sm text-gray-950 focus-visible:ring-0 dark:text-white"
                                />
                            </div>

                            <div class="grid grid-cols-7 gap-1">
                                <template
                                    x-for="(day, index) in dayLabels"
                                    x-bind:key="index"
                                >
                                    <div
                                        x-text="day"
                                        class="text-center text-xs font-medium text-gray-500 dark:text-gray-400"
                                    ></div>
                                </template>
                            </div>

                            <div
                                role="grid"
                                class="grid grid-cols-[repeat(7,_theme(spacing.7))] gap-1"
                            >
                                <template
                                    x-for="day in emptyDaysInFocusedMonth"
                                    x-bind:key="day"
                                >
                                    <div></div>
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
                                            'text-gray-950 dark:text-white': ! dayIsToday(day) && ! dayIsSelected(day),
                                            'cursor-pointer': ! dayIsDisabled(day),
                                            'text-primary-600 dark:text-primary-400':
                                                dayIsToday(day) &&
                                                ! dayIsSelected(day) &&
                                                focusedDate.date() !== day &&
                                                ! dayIsDisabled(day),
                                            'bg-gray-50 dark:bg-white/5':
                                                focusedDate.date() === day && ! dayIsSelected(day),
                                            'text-primary-600 bg-gray-50 dark:bg-white/5 dark:text-primary-400':
                                                dayIsSelected(day),
                                            'pointer-events-none': dayIsDisabled(day),
                                            'opacity-50': focusedDate.date() !== day && dayIsDisabled(day),
                                        }"
                                        class="rounded-full text-center text-sm leading-loose transition duration-75"
                                    ></div>
                                </template>
                            </div>
                        @endif

                        @if ($hasTime())
                            <div
                                class="flex items-center justify-center rtl:flex-row-reverse"
                            >
                                <input
                                    max="23"
                                    min="0"
                                    step="{{ $getHoursStep() }}"
                                    type="number"
                                    inputmode="numeric"
                                    x-model.debounce="hour"
                                    class="me-1 w-10 border-none bg-transparent p-0 text-center text-sm text-gray-950 focus-visible:ring-0 dark:text-white"
                                />

                                <span
                                    class="text-sm font-medium text-gray-500 dark:text-gray-400"
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
                                    class="me-1 w-10 border-none bg-transparent p-0 text-center text-sm text-gray-950 focus-visible:ring-0 dark:text-white"
                                />

                                @if ($hasSeconds())
                                    <span
                                        class="text-sm font-medium text-gray-500 dark:text-gray-400"
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
                                        class="me-1 w-10 border-none bg-transparent p-0 text-center text-sm text-gray-950 focus-visible:ring-0 dark:text-white"
                                    />
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </x-filament::input.wrapper>

    @if ($datalistOptions)
        <datalist id="{{ $id }}-list">
            @foreach ($datalistOptions as $option)
                <option value="{{ $option }}" />
            @endforeach
        </datalist>
    @endif
</x-dynamic-component>
