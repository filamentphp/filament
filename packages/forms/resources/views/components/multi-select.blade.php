<x-forms::field-wrapper
    :id="$getId()"
    :label="$getLabel()"
    :label-sr-only="$isLabelHidden()"
    :helper-text="$getHelperText()"
    :hint="$getHint()"
    :hint-icon="$getHintIcon()"
    :required="$isRequired()"
    :state-path="$getStatePath()"
>
    <div
        x-data="multiSelectFormComponent({
            getOptionLabelsUsing: async (values) => {
                return await $wire.getMultiSelectOptionLabels('{{ $getStatePath() }}')
            },
            getSearchResultsUsing: async (query) => {
                return await $wire.getMultiSelectSearchResults('{{ $getStatePath() }}', query)
            },
            isAutofocused: {{ $isAutofocused() ? 'true' : 'false' }},
            options: {{ json_encode($getOptions()) }},
            state: $wire.{{ $applyStateBindingModifiers('entangle(\'' . $getStatePath() . '\')') }},
        })"
        {!! ($id = $getId()) ? "id=\"{$id}\"" : null !!}
        {{ $attributes->merge($getExtraAttributes())->class([
            'block w-full transition duration-75 divide-y rounded-lg shadow-sm border focus-within:border-primary-600 focus-within:ring-1 focus-within:ring-primary-600',
            'border-gray-300' => ! $errors->has($getStatePath()),
            'border-danger-600 ring-danger-600' => $errors->has($getStatePath()),
            'filament-forms-multi-select-component',
        ]) }}
        {{ $getExtraAlpineAttributeBag() }}
    >
        @unless ($isDisabled())
            <div
                x-on:click.away="closeListbox()"
                x-on:blur="closeListbox()"
                x-on:keydown.escape.stop="closeListbox()"
                class="relative"
            >
                <div
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
                    class="relative rounded-lg overflow-hidden"
                >
                    <input
                        x-ref="search"
                        x-model.debounce.500ms="search"
                        x-on:keydown="if (! isOpen) openListbox()"
                        x-on:keydown.enter.stop.prevent="selectOption()"
                        x-on:keydown.arrow-up.stop.prevent="focusPreviousOption()"
                        x-on:keydown.arrow-down.stop.prevent="focusNextOption()"
                        placeholder="{{ $getPlaceholder() }}"
                        type="text"
                        autocomplete="off"
                        class="block w-full border-0"
                    />

                    <span class="absolute inset-y-0 right-0 rtl:right-auto rtl:left-0 flex items-center pr-2 rtl:pr-0 rtl:pl-2 pointer-events-none">
                        <svg x-show="! isLoading" x-cloak class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                            <path stroke="#6B7280" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 8l4 4 4-4" />
                        </svg>

                        <svg x-show="isLoading" class="animate-spin w-5 h-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M2 12C2 6.47715 6.47715 2 12 2V5C8.13401 5 5 8.13401 5 12H2Z" />>
                        </svg>
                    </span>
                </div>

                <div
                    x-ref="listbox"
                    x-show="isOpen"
                    x-transition:leave="ease-in duration-100"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    role="listbox"
                    x-bind:aria-activedescendant="focusedOptionIndex ? '{{ $getStatePath() }}' + 'Option' + focusedOptionIndex : null"
                    tabindex="-1"
                    x-cloak
                    class="absolute z-10 w-full my-1 bg-white border border-gray-300 rounded-lg shadow-sm focus:outline-none transition"
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
                                    x-show="state.indexOf(key) >= 0"
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
                            class="px-3 py-2 text-sm text-gray-700 cursor-default select-none"
                        ></div>
                    </ul>
                </div>
            </div>
        @endunless

        <div
            x-show="state.length"
            class="overflow-hidden rtl:space-x-reverse relative w-full px-1 py-1"
        >
            <div class="flex flex-wrap gap-1">
                <template class="inline" x-for="option in state" x-bind:key="option">
                    <button
                        @unless ($isDisabled())
                            x-on:click.stop="deselectOption(option)"
                        @endunless
                        type="button"
                        @class([
                            'inline-flex items-center justify-center h-6 px-2 my-1 text-sm font-medium tracking-tight text-primary-700 rounded-full bg-primary-500/10 space-x-1 rtl:space-x-reverse',
                            'cursor-default' => $isDisabled(),
                        ])
                    >
                        <span x-text="labels[option]"></span>

                        @unless ($isDisabled())
                            <x-heroicon-s-x class="w-3 h-3" />
                        @endunless
                    </button>
                </template>
            </div>
        </div>
    </div>
</x-forms::field-wrapper>
