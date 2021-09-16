<x-forms::field-wrapper
    :id="$getId()"
    :label="$getLabel()"
    :label-sr-only="$isLabelHidden()"
    :helper-text="$getHelperText()"
    :hint="$getHint()"
    :required="$isRequired()"
    :state-path="$getStatePath()"
>
    @unless ($isSearchable())
        <div x-data="{ hasValue: true }">
            <select
                {!! $isAutofocused() ? 'autofocus' : null !!}
                {!! $isDisabled() ? 'disabled' : null !!}
                id="{{ $getId() }}"
                {!! $isRequired() ? 'required' : null !!}
                {{ $applyStateBindingModifiers('wire:model') }}="{{ $getStatePath() }}"
                x-on:change="hasValue = $el.value"
                x-bind:class="{
                    'text-gray-900' => ! ! hasValue,
                    'text-gray-400' => ! hasValue,
                }"
                {{ $attributes->merge($getExtraAttributes())->class([
                    'block w-full h-10 transition duration-75 rounded-lg shadow-sm focus:border-primary-600 focus:ring-1 focus:ring-inset focus:ring-primary-600',
                    'border-gray-300' => ! $errors->has($getStatePath()),
                    'border-danger-600 ring-danger-600' => $errors->has($getStatePath()),
                ]) }}
            >
                <option value="" {!! $isRequired() ? 'disabled' : '' !!}>{{ $getPlaceholder() }}</option>

                @foreach ($getOptions() as $value => $label)
                    <option value="{{ $value }}">
                        {{ $label }}
                    </option>
                @endforeach
            </select>
        </div>
    @else
        <div
            x-data="selectFormComponent({
                getOptionLabelUsing: async (value) => {
                    return await $wire.getSelectOptionLabel('{{ $getStatePath() }}')
                },
                getSearchResultsUsing: async (query) => {
                    return await $wire.getSelectSearchResults('{{ $getStatePath() }}', query)
                },
                isAutofocused: {{ $isAutofocused() ? 'true' : 'false' }},
                options: {{ json_encode($getOptions()) }},
                state: $wire.{{ $applyStateBindingModifiers('entangle(\'' . $getStatePath() . '\')') }},
            })"
            x-on:click.away="closeListbox()"
            x-on:blur="closeListbox()"
            x-on:keydown.escape.stop="closeListbox()"
            {!! ($id = $getId()) ? "id=\"{$id}\"" : null !!}
            class="relative"
            {{ $attributes->merge($getExtraAttributes()) }}
        >
            <div
                @unless($isDisabled())
                    x-ref="button"
                    x-on:click="toggleListboxVisibility()"
                    x-on:keydown.enter.stop.prevent="isOpen ? selectOption() : openListbox()"
                    x-on:keydown.space="if (! isOpen) openListbox()"
                    x-on:keydown.backspace="if (! search) clearState()"
                    x-on:keydown.clear="if (! search) clearState()"
                    x-on:keydown.delete="if (! search) clearState()"
                    x-bind:aria-expanded="isOpen"
                    aria-haspopup="listbox"
                    tabindex="1"
                @endunless
                @class([
                    'relative flex items-center h-10 pl-3 pr-10 border bg-white overflow-hidden transition duration-75 rounded-lg shadow-sm focus-within:border-primary-600 focus-within:ring-1 focus-within:ring-inset focus-within:ring-primary-600 focus:outline-none',
                    'border-gray-300' => ! $errors->has($getStatePath()),
                    'border-danger-600 ring-danger-600' => $errors->has($getStatePath()),
                ])
            >
                <span
                    x-show="! isOpen"
                    x-text="label ?? '{{ $getPlaceholder() }}'"
                    class="absolute w-full bg-white"
                    x-bind:class="{
                        'text-gray-400': label === null,
                    }"
                ></span>

                @unless ($isDisabled())
                    <input
                        x-ref="search"
                        x-model.debounce.500="search"
                        x-on:keydown.enter.stop.prevent="selectOption()"
                        x-on:keydown.arrow-up.stop.prevent="focusPreviousOption()"
                        x-on:keydown.arrow-down.stop.prevent="focusNextOption()"
                        type="text"
                        autocomplete="off"
                        class="w-full my-1 p-0 border-0 focus:ring-0 focus:outline-none"
                    />

                    <span class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                        <svg x-show="! isLoading" x-cloak class="w-5 h-5"xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                            <path stroke="#6B7280" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 8l4 4 4-4" />
                        </svg>

                        <svg x-show="isLoading" class="w-5 h-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 50 50" fill="currentColor">
                            <path d="M6.306 28.014c1.72 10.174 11.362 17.027 21.536 15.307C38.016 41.6 44.87 31.958 43.15 21.784l-4.011.678c1.345 7.958-4.015 15.502-11.974 16.847-7.959 1.346-15.501-4.014-16.847-11.973l-4.011.678z" />

                            <animateTransform attributeType="xml" attributeName="transform" type="rotate" from="0 25 25" to="360 25 25" dur=".7s" repeatCount="indefinite" /></path>
                        </svg>
                    </span>
                @endunless
            </div>

            @unless($isDisabled())
                <div
                    x-ref="listbox"
                    x-show="isOpen"
                    x-transition:leave="transition ease-in duration-100"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    role="listbox"
                    x-bind:aria-activedescendant="focusedOptionIndex ? '{{ $getStatePath() }}' + 'Option' + focusedOptionIndex : null"
                    tabindex="-1"
                    x-cloak
                    class="absolute z-10 w-full my-1 bg-white border border-gray-300 rounded-lg shadow-sm focus:outline-none"
                >
                    <ul
                        x-ref="listboxOptionsList"
                        class="py-1 overflow-auto text-base leading-6 max-h-60 focus:outline-none"
                    >
                        <template x-for="(key, index) in Object.keys(options)" :key="key">
                            <li
                                x-bind:id="'{{ $getName() }}' + 'Option' + focusedOptionIndex"
                                x-on:click="selectOption(index)"
                                x-on:mouseenter="focusedOptionIndex = index"
                                x-on:mouseleave="focusedOptionIndex = null"
                                role="option"
                                x-bind:aria-selected="focusedOptionIndex === index"
                                x-bind:class="{
                                    'text-white bg-primary-500': index === focusedOptionIndex,
                                    'text-gray-900': index !== focusedOptionIndex,
                                }"
                                class="relative py-2 pl-3 h-10 flex items-center text-gray-900 cursor-default select-none pr-9"
                            >
                                <span
                                    x-text="Object.values(options)[index]"
                                    x-bind:class="{
                                        'font-medium': index === focusedOptionIndex,
                                        'font-normal': index !== focusedOptionIndex,
                                    }"
                                    class="block font-normal truncate"
                                ></span>

                                <span
                                    x-show="key === state"
                                    x-bind:class="{
                                        'text-white': index === focusedOptionIndex,
                                        'text-primary-500': index !== focusedOptionIndex,
                                    }"
                                    class="absolute inset-y-0 right-0 flex items-center pr-4 text-primary-500"
                                >
                                    <svg class="w-5 h-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd"
                                              d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                              clip-rule="evenodd" />
                                    </svg>
                                </span>
                            </li>
                        </template>

                        <div
                            x-show="! Object.keys(options).length"
                            x-text="! search || isLoading ? '{{ $getSearchPrompt() }}' : '{{ $getNoSearchResultsMessage() }}'"
                            class="px-3 py-2 text-sm text-gray-900 cursor-default select-none"
                        ></div>
                    </ul>
                </div>
            @endunless
        </div>
    @endif
</x-forms::field-wrapper>
