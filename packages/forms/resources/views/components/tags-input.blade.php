<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    <div
        x-ignore
        ax-load
        ax-load-src="/js/filament/forms/components/tags-input.js?v={{ \Composer\InstalledVersions::getVersion('filament/support') }}"
        x-data="tagsInputFormComponent({
            state: $wire.{{ $applyStateBindingModifiers('entangle(\'' . $getStatePath() . '\')') }},
        })"
        {{
            $attributes
                ->merge([
                    'id' => $getId(),
                ], escape: false)
                ->merge($getExtraAttributes(), escape: false)
                ->merge($getExtraAlpineAttributes(), escape: false)
                ->class(['filament-forms-tags-input-component'])
        }}
    >
        <div
            x-show="state.length || {{ $isDisabled() ? 'false' : 'true' }}"
            @class([
                'block w-full transition duration-75 divide-y rounded-lg shadow-sm border overflow-hidden focus-within:border-primary-500 focus-within:ring-1 focus-within:ring-primary-500 dark:divide-gray-600',
                'border-gray-300 dark:border-gray-600' => ! $errors->has($getStatePath()),
                'border-danger-600 ring-1 ring-inset ring-danger-600 dark:border-danger-400 dark:ring-danger-400' => $errors->has($getStatePath()),
            ])
        >
            @unless ($isDisabled())
                <div>
                    <input
                        autocomplete="off"
                        @if ($isAutofocused()) autofocus @endif
                        id="{{ $getId() }}"
                        list="{{ $getId() }}-suggestions"
                        @if ($placeholder = $getPlaceholder()) placeholder="{{ $placeholder }}" @endif
                        type="text"
                        dusk="filament.forms.{{ $getStatePath() }}"
                        x-on:keydown.enter.stop.prevent="createTag()"
                        x-on:keydown.,.stop.prevent="createTag()"
                        x-on:blur="createTag()"
                        x-on:paste="$nextTick(() => {
                            if (newTag.includes(',')) {
                                newTag.split(',').forEach((tag) => {
                                    newTag = tag

                                    createTag()
                                })
                            }
                        })"
                        x-model="newTag"
                        {{ $getExtraInputAttributeBag()->class(['webkit-calendar-picker-indicator:opacity-0 block w-full border-0 dark:bg-gray-700 dark:text-gray-200 dark:placeholder-gray-400']) }}
                    />

                    <datalist id="{{ $getId() }}-suggestions">
                        @foreach ($getSuggestions() as $suggestion)
                            <template x-if="! state.includes(@js($suggestion))" x-bind:key="@js($suggestion)">
                                <option value="{{ $suggestion }}" />
                            </template>
                        @endforeach
                    </datalist>
                </div>
            @endunless

            <div
                x-show="state.length"
                x-cloak
                class="relative w-full p-2 overflow-hidden"
            >
                <div class="flex flex-wrap gap-1">
                    <template class="hidden" x-for="tag in state" x-bind:key="tag">
                        <button
                            @unless ($isDisabled())
                                x-on:click="deleteTag(tag)"
                            @endunless
                            type="button"
                            x-bind:dusk="'filament.forms.{{ $getStatePath() }}' + '.tag.' + tag + '.delete'"
                            @class([
                                'inline-flex items-center justify-center min-h-6 px-2 py-0.5 text-sm font-medium tracking-tight text-primary-700 rounded-xl bg-primary-500/10 space-x-1 rtl:space-x-reverse dark:text-primary-500',
                                'cursor-default' => $isDisabled(),
                            ])
                        >
                            <span class="text-start" x-text="tag"></span>

                            @unless ($isDisabled())
                                <x-filament::icon
                                    name="heroicon-m-x-mark"
                                    alias="filament-forms::components.tags-input.buttons.delete-tag"
                                    size="h-3 w-3"
                                    class="shrink-0"
                                />
                            @endunless
                        </button>
                    </template>
                </div>
            </div>
        </div>
    </div>
</x-dynamic-component>
