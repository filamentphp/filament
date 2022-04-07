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
    <div
        x-data="multiSelectFormComponent({
            getOptionLabelsUsing: async () => {
                return await $wire.getMultiSelectOptionLabels('{{ $getStatePath() }}')
            },
            getOptionsUsing: async () => {
                return await $wire.getMultiSelectOptions('{{ $getStatePath() }}')
            },
            getSearchResultsUsing: async (searchQuery) => {
                return await $wire.getMultiSelectSearchResults('{{ $getStatePath() }}', searchQuery)
            },
            isAutofocused: {{ $isAutofocused() ? 'true' : 'false' }},
            hasDynamicOptions: {{ $hasDynamicOptions() ? 'true' : 'false' }},
            options: {{ json_encode($getOptions()) }},
            state: $wire.{{ $applyStateBindingModifiers('entangle(\'' . $getStatePath() . '\')') }},
        })"
        {!! ($id = $getId()) ? "id=\"{$id}\"" : null !!}
        {{ $attributes->merge($getExtraAttributes())->class([
            'block w-full transition duration-75 divide-y rounded-lg shadow-sm border focus-within:border-primary-600 focus-within:ring-1 focus-within:ring-primary-600 filament-forms-multi-select-component',
            'dark:bg-gray-700 dark:divide-gray-600' => config('forms.dark_mode'),
            'border-gray-300' => ! $errors->has($getStatePath()),
            'dark:border-gray-600' => (! $errors->has($getStatePath())) && config('forms.dark_mode'),
            'border-danger-600 ring-danger-600' => $errors->has($getStatePath()),
        ]) }}
        {{ $getExtraAlpineAttributeBag() }}
    >
        @unless ($isDisabled())
            <div
                x-on:click.away="closeListbox()"
                x-on:blur="closeListbox()"
                x-on:keydown.escape.stop="closeListbox()"
                class="relative"
                dusk="filament.forms.{{ $getStatePath() }}.close"
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
                    class="relative overflow-hidden rounded-lg"
                >
                    <input
                        x-ref="search"
                        x-model="search"
                        x-on:keydown="if (! isOpen) openListbox()"
                        x-on:keydown.enter.stop.prevent="selectOption()"
                        x-on:keydown.arrow-up.stop.prevent="focusPreviousOption()"
                        x-on:keydown.arrow-down.stop.prevent="focusNextOption()"
                        placeholder="{{ $getPlaceholder() }}"
                        type="text"
                        autocomplete="off"
                        dusk="filament.forms.{{ $getStatePath() }}"
                        @class([
                            'block w-full border-0',
                            'dark:bg-gray-700 dark:placeholder-gray-400' => config('forms.dark_mode'),
                        ])
                    />

                    <span class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none rtl:right-auto rtl:left-0 rtl:pr-0 rtl:pl-2">
                        <svg x-show="! isLoading" class="w-5 h-5" x-bind:class="isOpen && 'rotate-180'" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                            <path stroke="#6B7280" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 8l4 4 4-4" />
                        </svg>

                        <svg x-show="isLoading" x-cloak class="w-5 h-5 text-gray-400 animate-spin" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
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
                    @class([
                        'absolute z-30 w-full my-1 bg-white border border-gray-300 rounded-lg shadow-md focus:outline-none transition',
                        'dark:bg-gray-700 dark:border-gray-600' => config('forms.dark_mode'),
                    ])
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
                                    'text-gray-900 @if (config('forms.dark_mode')) dark:text-gray-200 @endif': index !== focusedOptionIndex,
                                }"
                                class="relative flex items-center py-2 pl-3 text-gray-900 cursor-default select-none pr-9"
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
                            @class([
                                'px-3 py-2 text-sm text-gray-700 cursor-default select-none',
                                'dark:text-gray-200' => config('forms.dark_mode'),
                            ])
                        >
                            <span x-show="! hasNoSearchResults">
                                {{ $getSearchPrompt() }}
                            </span>

                            <span x-show="hasNoSearchResults">
                                {{ $getNoSearchResultsMessage() }}
                            </span>
                        </div>
                    </ul>
                </div>
            </div>
        @endunless

        <div
            x-show="state.length"
            x-cloak
            class="relative w-full p-2 overflow-hidden rtl:space-x-reverse"
        >
            <div class="flex flex-wrap gap-1">
                <template class="hidden" x-for="option in state" x-bind:key="option">
                    <button
                        @unless ($isDisabled())
                            x-on:click.stop="deselectOption(option)"
                        @endunless
                        type="button"
                        @class([
                            'inline-flex items-center justify-center min-h-6 px-2 py-0.5 text-sm font-medium tracking-tight text-primary-700 rounded-xl bg-primary-500/10 space-x-1 rtl:space-x-reverse',
                            'dark:text-primary-500' => config('forms.dark_mode'),
                            'cursor-default' => $isDisabled(),
                        ])
                    >
                        <span class="text-left" x-text="labels[option]"></span>

                        @unless ($isDisabled())
                            <x-heroicon-s-x class="w-3 h-3 shrink-0" />
                        @endunless
                    </button>
                </template>
            </div>
        </div>
    </div>
</x-dynamic-component>
