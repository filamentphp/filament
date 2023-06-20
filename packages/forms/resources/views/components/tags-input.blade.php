<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    @php
        $id = $getId();
        $isDisabled = $isDisabled();
        $splitKeys = $getSplitKeys();
        $statePath = $getStatePath();
    @endphp

    <div
        x-ignore
        ax-load
        ax-load-src="{{ \Filament\Support\Facades\FilamentAsset::getAlpineComponentSrc('tags-input', 'filament/forms') }}"
        x-data="tagsInputFormComponent({
                    state: $wire.{{ $applyStateBindingModifiers("entangle('{$statePath}')") }},
                })"
        {{
            $attributes
                ->merge($getExtraAttributes(), escape: false)
                ->merge($getExtraAlpineAttributes(), escape: false)
                ->class(['filament-forms-tags-input-component'])
        }}
    >
        <div x-show="state?.length || @js(! $isDisabled)">
            <div
                @class([
                    'block w-full overflow-hidden rounded-lg border shadow-sm transition duration-75 focus-within:ring-1 sm:text-sm',
                    'border-gray-300 focus-within:border-primary-500 focus-within:ring-primary-500 dark:border-gray-600 dark:focus-within:border-primary-500' => ! $errors->has($statePath),
                    'border-danger-600 ring-danger-600 dark:border-danger-400 dark:ring-danger-400' => $errors->has($statePath),
                ])
            >
                @unless ($isDisabled)
                    <div>
                        <input
                            autocomplete="off"
                            @if ($isAutofocused()) autofocus @endif
                            id="{{ $id }}"
                            list="{{ $id }}-suggestions"
                            @if ($placeholder = $getPlaceholder()) placeholder="{{ $placeholder }}" @endif
                            type="text"
                            dusk="filament.forms.{{ $statePath }}"
                            x-on:keydown="
                                () => {
                                    if (['Enter', ...@js($splitKeys)].includes($event.key)) {
                                        $event.preventDefault()
                                        $event.stopPropagation()

                                        createTag()
                                    }
                                }
                            "
                            x-on:blur="createTag()"
                            x-on:paste="
                                $nextTick(() => {
                                    const pattern = @js($splitKeys).map((key) =>
                                        key.replace(/[/\-\\^$*+?.()|[\]{}]/g, '\\$&'),
                                    ).join('|')

                                    newTag.split(new RegExp(pattern, 'g')).forEach((tag) => {
                                        newTag = tag

                                        createTag()
                                    })
                                })
                            "
                            x-model="newTag"
                            {{ $getExtraInputAttributeBag()->class(['webkit-calendar-picker-indicator:opacity-0 block w-full border-0 dark:bg-gray-700 dark:text-gray-200 dark:placeholder-gray-400 sm:text-sm']) }}
                        />

                        <datalist id="{{ $id }}-suggestions">
                            @foreach ($getSuggestions() as $suggestion)
                                <template
                                    x-if="! state.includes(@js($suggestion))"
                                    x-bind:key="@js($suggestion)"
                                >
                                    <option value="{{ $suggestion }}" />
                                </template>
                            @endforeach
                        </datalist>
                    </div>
                @endunless

                <div wire:ignore>
                    <template x-if="state?.length" x-cloak>
                        <div
                            @class([
                                'relative flex w-full flex-wrap gap-1 overflow-hidden p-2',
                                'border-t dark:border-gray-600' => ! $isDisabled,
                            ])
                        >
                            <template
                                class="hidden"
                                x-for="tag in state"
                                x-bind:key="tag"
                            >
                                <button
                                    @unless ($isDisabled)
                                        x-on:click="deleteTag(tag)"
                                    @endunless
                                    type="button"
                                    x-bind:dusk="'filament.forms.{{ $statePath }}' + '.tag.' + tag + '.delete'"
                                    @class([
                                        'min-h-6 inline-flex items-center justify-center space-x-1 rounded-xl bg-primary-500/10 px-2 py-0.5 text-sm font-medium tracking-tight text-primary-700 rtl:space-x-reverse dark:text-primary-500',
                                        'cursor-default' => $isDisabled,
                                    ])
                                >
                                    <span
                                        class="text-start"
                                        x-text="tag"
                                    ></span>

                                    @unless ($isDisabled)
                                        <x-filament::icon
                                            name="heroicon-m-x-mark"
                                            alias="forms::components.tags-input.actions.delete-tag"
                                            size="h-3 w-3"
                                            class="shrink-0"
                                        />
                                    @endunless
                                </button>
                            </template>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>
</x-dynamic-component>
