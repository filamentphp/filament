<x-forms::field-wrapper
    :id="$getId()"
    :label="$getLabel()"
    :helper-text="$getHelperText()"
    :hint="$getHint()"
    :required="$isRequired()"
    :state-path="$getStatePath()"
>
    <div
        x-data="tagsInputFormComponent({
            state: $wire.{{ $applyStateBindingModifiers('entangle(\'' . $getStatePath() . '\')') }},
        })"
        {!! ($id = $getId()) ? "id=\"{$id}\"" : null !!}
        {{ $attributes->merge($getExtraAttributes()) }}
    >
        <div
            x-show="state.length || {{ $isDisabled() ? 'false' : 'true' }}"
            @class([
                'block w-full transition duration-75 divide-y rounded-lg shadow-sm border overflow-hidden focus-within:border-primary-600 focus-within:ring-1 focus-within:ring-primary-600',
                'border-gray-300' => ! $errors->has($getStatePath()),
                'border-danger-600 ring-danger-600' => $errors->has($getStatePath()),
            ])
        >
            @unless ($isDisabled())
                <input
                    autocomplete="off"
                    {!! $isAutofocused() ? 'autofocus' : null !!}
                    {!! $getPlaceholder() ? 'placeholder="' . __($getPlaceholder()) . '"' : null !!}
                    type="text"
                    x-on:keydown.enter.stop.prevent="createTag()"
                    x-model="newTag"
                    class="block w-full border-0"
                />
            @endunless

            <div
                x-show="state.length"
                class="bg-white space-x-1 rtl:space-x-reverse relative w-full px-2 py-1"
            >
                <div class="-ml-1">
                    <template class="inline" x-for="tag in state" x-bind:key="tag">
                        <button
                            @unless ($isDisabled())
                                x-on:click="deleteTag(tag)"
                            @endunless
                            type="button"
                            @class([
                                'inline-flex items-center justify-center h-6 px-2 my-1 text-sm font-semibold tracking-tight text-primary-700 rounded-full bg-primary-500/10 space-x-1',
                                'cursor-default' => $isDisabled(),
                            ])
                        >
                            <span x-text="tag"></span>

                            @unless ($isDisabled())
                                <x-heroicon-s-x class="w-3 h-3" />
                            @endunless
                        </button>
                    </template>
                </div>
            </div>
        </div>
    </div>
</x-forms::field-wrapper>
