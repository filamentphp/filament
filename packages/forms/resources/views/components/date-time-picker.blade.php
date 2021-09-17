@once
    @push('scripts')
        <script src="https://unpkg.com/dayjs@1.10.4/locale/{{ Str::of(app()->getLocale())->lower()->kebab() }}.js"></script>
    @endpush
@endonce

@php
    if (! $hasTime()) {
        if ($getDisplayFormat() === 'F j, Y H:i:s') $displayFormat('F j, Y');
        if ($getFormat() === 'F j, Y H:i:s') $format('Y-m-d');
    }

    if ($hasTime() && ! $hasSeconds()) {
        if ($getDisplayFormat() === 'F j, Y H:i:s') $displayFormat('F j, Y H:i');
    }
@endphp

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
            locale: '{{ Str::of(app()->getLocale())->lower()->kebab() }}',
            maxDate: '{{ $getMaxDate() }}',
            minDate: '{{ $getMinDate() }}',
            state: $wire.{{ $applyStateBindingModifiers('entangle(\'' . $getStatePath() . '\')') }},
        })"
        x-on:click.away="closePicker()"
        x-on:keydown.escape.stop="closePicker()"
        x-on:blur="closePicker()"
        {!! ($id = $getId()) ? "id=\"{$id}\"" : null !!}
        class="relative"
        {{ $attributes->merge($getExtraAttributes()) }}
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
            class="bg-white relative w-full border pl-3 pr-10 py-2 text-left cursor-default rounded-lg shadow-sm focus-within:border-primary-600 focus-within:ring-1 focus-within:ring-inset focus-within:ring-primary-600 {{ $isDisabled() ? 'text-gray-500' : '' }} {{ $errors->has($getStatePath()) ? 'border-danger-600 motion-safe:animate-shake' : 'border-gray-300' }}"
        >
            <input
                readonly
                placeholder="{{ $getPlaceholder() }}"
                x-model="displayText"
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
                class="absolute z-10 w-64 p-4 my-1 bg-white border border-gray-300 rounded-lg shadow-sm"
            >
                <div class="space-y-3">
                    <div class="flex items-center justify-between space-x-1 rtl:space-x-reverse">
                        <select
                            x-model="focusedMonth"
                            class="flex-grow p-0 text-lg font-medium text-gray-800 border-0 cursor-pointer focus:ring-0 focus:outline-none"
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

                    <div class="grid grid-cols-7 gap-1">
                        <template x-for="(day, index) in getDayLabels()" :key="index">
                            <div
                                x-text="day"
                                class="text-xs font-medium text-center text-gray-800"
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
                                    'text-gray-700': ! dayIsSelected(day),
                                    'cursor-pointer': ! dayIsDisabled(day),
                                    'bg-primary-50': dayIsToday(day) && ! dayIsSelected(day) && focusedDate.date() !== day && ! dayIsDisabled(day),
                                    'bg-primary-200': focusedDate.date() === day && ! dayIsSelected(day),
                                    'bg-primary-500 text-white': dayIsSelected(day),
                                    'cursor-not-allowed': dayIsDisabled(day),
                                    'opacity-50': focusedDate.date() !== day && dayIsDisabled(day),
                                }"
                                class="text-sm leading-none leading-loose text-center transition duration-100 ease-in-out rounded-full"
                            ></div>
                        </template>
                    </div>

                    @if ($hasTime())
                        <div class="flex items-center justify-center py-2 bg-gray-100 rounded-lg">
                            <input
                                max="23"
                                min="0"
                                type="number"
                                x-model.debounce="hour"
                                class="w-16 p-0 pr-1 text-xl text-center text-gray-700 bg-gray-100 border-0 focus:ring-0 focus:outline-none"
                            />

                            <span class="text-xl font-medium text-gray-700 bg-gray-100">:</span>

                            <input
                                max="59"
                                min="0"
                                type="number"
                                x-model.debounce="minute"
                                class="w-16 p-0 pr-1 text-xl text-center text-gray-700 bg-gray-100 border-0 focus:ring-0 focus:outline-none"
                            />

                            @if ($hasSeconds())
                                <span class="text-xl font-medium text-gray-700 bg-gray-100">:</span>

                                <input
                                    max="59"
                                    min="0"
                                    type="number"
                                    x-model.debounce="second"
                                    class="w-16 p-0 pr-1 text-xl text-center text-gray-700 bg-gray-100 border-0 focus:ring-0 focus:outline-none"
                                />
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        @endunless
    </div>
</x-forms::field-wrapper>
