@php
    $icon = $getIcon();
@endphp

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
    <div
        x-data="dateTimePickerFormComponent({
                    displayFormat:
                        '{{ convert_date_format($getDisplayFormat())->to('day.js') }}',
                    firstDayOfWeek: {{ $getFirstDayOfWeek() }},
                    isAutofocused: @js($isAutofocused()),
                    locale: @js(app()->getLocale()),
                    shouldCloseOnDateSelection: @js($shouldCloseOnDateSelection()),
                    state: $wire.{{ $applyStateBindingModifiers('entangle(\'' . $getStatePath() . '\')') }},
                })"
        x-on:keydown.esc="isOpen() && $event.stopPropagation()"
        {{ $getExtraAlpineAttributeBag() }}
        {{
            $attributes
                ->merge($getExtraAttributes())
                ->class(['filament-forms-date-time-picker-component relative'])
        }}
    >
        <input x-ref="maxDate" type="hidden" value="{{ $getMaxDate() }}" />
        <input x-ref="minDate" type="hidden" value="{{ $getMinDate() }}" />
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
            dusk="filament.forms.{{ $getStatePath() }}.open"
            type="button"
            tabindex="-1"
            @if ($isDisabled()) disabled @endif
            {{
                $getExtraTriggerAttributeBag()->class([
                    'relative w-full cursor-default rounded-lg border bg-white py-2 text-start shadow-sm outline-none',
                    'focus-within:border-primary-500 focus-within:ring-1 focus-within:ring-inset focus-within:ring-primary-500' => ! $isDisabled(),
                    'dark:bg-gray-700' => config('forms.dark_mode'),
                    'border-gray-300' => ! $errors->has($getStatePath()),
                    'dark:border-gray-600' => (! $errors->has($getStatePath())) && config('forms.dark_mode'),
                    'border-danger-600' => $errors->has($getStatePath()),
                    'dark:border-danger-400' => $errors->has($getStatePath()) && config('forms.dark_mode'),
                    'opacity-70' => $isDisabled(),
                    'dark:text-gray-300' => $isDisabled() && config('forms.dark_mode'),
                    'px-3' => $icon === false,
                    'pl-3 pr-10 rtl:pl-10 rtl:pr-3' => $icon !== false,
                ])
            }}
        >
            <input
                readonly
                placeholder="{{ $getPlaceholder() }}"
                wire:key="{{ $this->id }}.{{ $getStatePath() }}.{{ $field::class }}.display-text"
                x-model="displayText"
                {!! ($id = $getId()) ? "id=\"{$id}\"" : null !!}
                @class([
                    'h-full w-full border-0 bg-transparent p-0 placeholder-gray-400 outline-none focus:placeholder-gray-500 focus:outline-none focus:ring-0',
                    'dark:bg-gray-700 dark:placeholder-gray-400' => config('forms.dark_mode'),
                    'cursor-default' => $isDisabled(),
                ])
            />

            @if ($icon !== false)
                <span
                    class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2 rtl:left-0 rtl:right-auto rtl:pl-2"
                >
                    <x-dynamic-component
                        :component="$icon ?? 'heroicon-o-calendar'"
                        @class([
                            'w-5 h-5 text-gray-400 hover:text-gray-800 focus:text-primary-600',
                            'dark:text-gray-400 dark:hover:text-gray-200 dark:focus:text-primary-600' => config('forms.dark_mode'),
                        ])
                    />
                </span>
            @endif
        </button>

        <div
            x-ref="panel"
            x-cloak
            x-float.placement.bottom-start.offset.flip.shift="{ offset: 8 }"
            wire:ignore.self
            wire:key="{{ $this->id }}.{{ $getStatePath() }}.{{ $field::class }}.panel"
            @class([
                'absolute z-10 my-1 hidden rounded-lg border border-gray-300 bg-white shadow-md',
                'dark:border-gray-600 dark:bg-gray-700' => config('forms.dark_mode'),
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
                            @class([
                                'grow cursor-pointer border-0 px-1 py-0 text-lg font-medium text-gray-800 outline-none focus:ring-0',
                                'dark:bg-gray-700 dark:text-gray-200' => config('forms.dark_mode'),
                            ])
                            dusk="filament.forms.{{ $getStatePath() }}.focusedMonth"
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
                            @class([
                                'w-20 border-0 p-0 text-end text-lg outline-none focus:ring-0',
                                'dark:bg-gray-700 dark:text-gray-200' => config('forms.dark_mode'),
                            ])
                            dusk="filament.forms.{{ $getStatePath() }}.focusedYear"
                        />
                    </div>

                    <div class="grid grid-cols-7 gap-1">
                        <template
                            x-for="(day, index) in dayLabels"
                            :key="index"
                        >
                            <div
                                x-text="day"
                                @class([
                                    'text-center text-xs font-medium text-gray-800',
                                    'dark:text-gray-200' => config('forms.dark_mode'),
                                ])
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
                                    'text-gray-700 @if (config('forms.dark_mode')) dark:text-gray-300 @endif': ! dayIsSelected(day),
                                    'cursor-pointer': ! dayIsDisabled(day),
                                    'bg-primary-50 @if (config('forms.dark_mode')) dark:bg-primary-100 dark:text-gray-600 @endif': dayIsToday(day) && ! dayIsSelected(day) && focusedDate.date() !== day && ! dayIsDisabled(day),
                                    'bg-primary-200 @if (config('forms.dark_mode')) dark:text-gray-600 @endif': focusedDate.date() === day && ! dayIsSelected(day),
                                    'bg-primary-500 text-white': dayIsSelected(day),
                                    'cursor-not-allowed pointer-events-none': dayIsDisabled(day),
                                    'opacity-50': focusedDate.date() !== day && dayIsDisabled(day),
                                }"
                                x-bind:dusk="'filament.forms.{{ $getStatePath() }}' + '.focusedDate.' + day"
                                class="rounded-full text-center text-sm leading-loose transition duration-100 ease-in-out"
                            ></div>
                        </template>
                    </div>
                @endif

                @if ($hasTime())
                    <div
                        @class([
                            'flex items-center justify-center rounded-lg bg-gray-50 py-2 rtl:flex-row-reverse',
                            'dark:bg-gray-800' => config('forms.dark_mode'),
                        ])
                    >
                        <input
                            max="23"
                            min="0"
                            step="{{ $getHoursStep() }}"
                            type="number"
                            inputmode="numeric"
                            x-model.debounce="hour"
                            @class([
                                'w-16 border-0 bg-gray-50 p-0 pr-1 text-center text-xl text-gray-700 outline-none focus:ring-0',
                                'dark:bg-gray-800 dark:text-gray-200' => config('forms.dark_mode'),
                            ])
                            dusk="filament.forms.{{ $getStatePath() }}.hour"
                        />

                        <span
                            @class([
                                'bg-gray-50 text-xl font-medium text-gray-700',
                                'dark:bg-gray-800 dark:text-gray-200' => config('forms.dark_mode'),
                            ])
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
                            @class([
                                'w-16 border-0 bg-gray-50 p-0 pr-1 text-center text-xl text-gray-700 outline-none focus:ring-0',
                                'dark:bg-gray-800 dark:text-gray-200' => config('forms.dark_mode'),
                            ])
                            dusk="filament.forms.{{ $getStatePath() }}.minute"
                        />

                        @if ($hasSeconds())
                            <span
                                @class([
                                    'bg-gray-50 text-xl font-medium text-gray-700',
                                    'dark:bg-gray-800 dark:text-gray-200' => config('forms.dark_mode'),
                                ])
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
                                dusk="filament.forms.{{ $getStatePath() }}.second"
                                @class([
                                    'w-16 border-0 bg-gray-50 p-0 pr-1 text-center text-xl text-gray-700 outline-none focus:ring-0',
                                    'dark:bg-gray-800 dark:text-gray-200' => config('forms.dark_mode'),
                                ])
                            />
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-dynamic-component>
