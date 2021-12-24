@once
    @push('scripts')
        <script src="//unpkg.com/dayjs@1.10.4/dayjs.min.js"></script>
        <script src="//unpkg.com/dayjs@1.10.4/plugin/localeData.js"></script>
        <script>
            dayjs.extend(window.dayjs_plugin_localeData)

            window.dayjs_locale = dayjs.locale()
        </script>
        <script src="//unpkg.com/dayjs@1.10.4/locale/{{ strtolower(str_replace('_', '-', app()->getLocale())) }}.js"></script>
    @endpush
@endonce

<x-forms::field-wrapper
    :id="$getId()"
    :label="$getLabel()"
    :label-sr-only="$isLabelHidden()"
    :helper-text="$getHelperText()"
    :hint="$getHint()"
    :required="$isRequired()"
    :state-path="$getStatePath()"
>
    <div
        x-data="dateTimePickerFormComponent({
            displayFormat: '{{ convert_date_format($getDisplayFormat())->to('day.js') }}',
            firstDayOfWeek: {{ $getFirstDayOfWeek() }},
            format: '{{ convert_date_format($getFormat())->to('day.js') }}',
            isAutofocused: {{ $isAutofocused() ? 'true' : 'false' }},
            maxDate: '{{ $getMaxDate() }}',
            minDate: '{{ $getMinDate() }}',
            state: $wire.{{ $applyStateBindingModifiers('entangle(\'' . $getStatePath() . '\')') }},
        })"
        x-on:click.away="closePicker()"
        x-on:keydown.escape.stop="closePicker()"
        x-on:blur="closePicker()"
        {{ $attributes->merge($getExtraAttributes())->class(['relative']) }}
        {{ $getExtraAlpineAttributeBag() }}
    >
        <button
            @unless($isDisabled())
                x-ref="button"
                x-on:click="togglePickerVisibility()"
                x-on:keydown.enter.stop.prevent="open ? selectDate() : openPicker()"
                x-on:keydown.arrow-left.stop.prevent="focusPreviousDay()"
                x-on:keydown.arrow-right.stop.prevent="focusNextDay()"
                x-on:keydown.arrow-up.stop.prevent="focusPreviousWeek()"
                x-on:keydown.arrow-down.stop.prevent="focusNextWeek()"
                x-on:keydown.backspace.stop.prevent="clearState()"
                x-on:keydown.clear.stop.prevent="clearState()"
                x-on:keydown.delete.stop.prevent="clearState()"
                x-bind:aria-expanded="open"
                aria-label="{{ $getPlaceholder() }}"
            @endunless
            type="button"
            {{ $getExtraTriggerAttributeBag()->class([
                'bg-white relative w-full border pl-3 pr-10 py-2 text-left cursor-default rounded-lg shadow-sm focus-within:border-primary-600 focus-within:ring-1 focus-within:ring-inset focus-within:ring-primary-600',
                'border-gray-300' => ! $errors->has($getStatePath()),
                'border-danger-600 motion-safe:animate-shake' => $errors->has($getStatePath()),
                'text-gray-500' => $isDisabled(),
            ]) }}
        >
            <input
                readonly
                placeholder="{{ $getPlaceholder() }}"
                x-model="displayText"
                {!! ($id = $getId()) ? "id=\"{$id}\"" : null !!}
                class="w-full h-full p-0 placeholder-gray-400 border-0 focus:placeholder-gray-500 focus:ring-0 focus:outline-none"
            />

            <span class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                <svg class="w-5 h-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </span>
        </button>

        @unless ($isDisabled())
            <div
                x-ref="picker"
                x-on:click.away="closePicker()"
                x-show.transition="open"
                aria-modal="true"
                role="dialog"
                x-cloak
                @class([
                    'absolute z-10 my-1 bg-white border border-gray-300 rounded-lg shadow-sm',
                    'p-4 w-64' => $hasDate(),
                ])
            >
                <div class="space-y-3">
                    @if ($hasDate())
                        <div class="flex items-center justify-between space-x-1 rtl:space-x-reverse">
                            <select
                                x-model="focusedMonth"
                                class="grow p-0 text-lg font-medium text-gray-800 border-0 cursor-pointer focus:ring-0 focus:outline-none"
                            >
                                <template x-for="(month, index) in dayjs.months()">
                                    <option x-bind:value="index" x-text="month"></option>
                                </template>
                            </select>

                            <input
                                type="number"
                                x-model.debounce="focusedYear"
                                class="w-20 p-0 text-lg text-right border-0 focus:ring-0 focus:outline-none"
                            />
                        </div>

                    

                        <div role="grid" class="grid grid-cols-7 gap-1">
                            <template x-for="day in emptyDaysInFocusedMonth" x-bind:key="day">
                                <div class="text-sm text-center border border-transparent"></div>
                            </template>

                            <template x-for="(month, index) in dayjs.months()" x-bind:key="month">
                                <div
                                    x-text="month"
                                    x-on:click="monthIsDisabled(index) || selectDate(index)"
                                    x-on:mouseenter="setFocusedMonth(index)"
                                    role="option"
                                    x-bind:aria-selected="focusedDate.date() === month"
                                    x-bind:class="{
                                        'text-gray-700': ! monthIsSelected(index),
                                        'cursor-pointer': ! monthIsDisabled(index),
                                        'bg-primary-50': monthIsThisMonth(index) && ! monthIsSelected(index) && focusedDate.date() !== index && ! monthIsDisabled(index),
                                        'bg-primary-200': focusedDate.date() === index && ! monthIsSelected(index),
                                        'bg-primary-500 text-white': monthIsSelected(index),
                                        'cursor-not-allowed': monthIsDisabled(index),
                                        'opacity-50': focusedDate.date() !== index && monthIsDisabled(index),
                                    }"
                                    class="text-sm leading-none leading-loose text-center transition duration-100 ease-in-out rounded-full"
                                ></div>
                            </template>
                        </div>
                    @endif

                    
                </div>
            </div>
        @endunless
    </div>
</x-forms::field-wrapper>
