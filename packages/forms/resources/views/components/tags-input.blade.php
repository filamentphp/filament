@pushonce('filament-scripts:tags-input-component')
    <script>
        function tagsInput(config) {
            return {
                hasError: false,

                newTag: '',

                separator: config.separator,

                tags: [],

                value: config.value,

                createTag: function () {
                    this.newTag = this.newTag.trim()

                    if (this.newTag === '' || this.tags.includes(this.newTag)) {
                        this.hasError = true

                        return
                    }

                    this.tags.push(this.newTag)

                    this.newTag = ''
                },

                deleteTag: function (tagToDelete) {
                    this.tags = this.tags.filter((tag) => tag !== tagToDelete)
                },

                init: function () {
                    if (this.value !== '' && this.value !== null) this.tags = this.value.trim().split(this.separator).filter(tag => tag !== '')

                    this.$watch('newTag', () => this.hasError = false)

                    this.$watch('tags', () => {
                        this.value = this.tags.join(this.separator)
                    })

                    this.$watch('value', () => {
                        try {
                            let expectedTags = this.value.trim().split(this.separator).filter(tag => tag !== '')

                            if (
                                this.tags.length === expectedTags.length &&
                                this.tags.filter((tag) => ! expectedTags.includes(tag)).length === 0
                            ) return

                            this.tags = expectedTags
                        } catch (error) {
                            this.tags = []
                        }
                    })
                },
            }
        }
    </script>
@endpushonce

<x-forms::field-group
    :column-span="$formComponent->getColumnSpan()"
    :error-key="$formComponent->getName()"
    :for="$formComponent->getId()"
    :help-message="$formComponent->getHelpMessage()"
    :hint="$formComponent->getHint()"
    :label="$formComponent->getLabel()"
    :required="$formComponent->isRequired()"
>
    <div
        x-data="tagsInput({
            separator: '{{ $formComponent->getSeparator() }}',
            @if (Str::of($formComponent->getBindingAttribute())->startsWith('wire:model'))
                value: @entangle($formComponent->getName()){{ Str::of($formComponent->getBindingAttribute())->after('wire:model') }},
            @endif
        })"
        x-init="init()"
        {!! $formComponent->getId() ? "id=\"{$formComponent->getId()}\"" : null !!}
        {!! Filament\format_attributes($formComponent->getExtraAttributes()) !!}
    >
        @unless (Str::of($formComponent->getBindingAttribute())->startsWith(['wire:model', 'x-model']))
            <input
                x-model="value"
                {!! $formComponent->getName() ? "{$formComponent->getBindingAttribute()}=\"{$formComponent->getName()}\"" : null !!}
                type="hidden"
            />
        @endif

        <div x-show="tags.length || {{ $formComponent->isDisabled() ? 'false' : 'true' }}" class="rounded shadow-sm border overflow-hidden focus-within:border-blue-300 focus-within:ring focus-within:ring-blue-200 focus-within:ring-opacity-50 {{ $errors->has($formComponent->getName()) ? 'border-danger-600 motion-safe:animate-shake' : 'border-gray-300' }}">
            @unless ($formComponent->isDisabled())
                <input
                    autocomplete="off"
                    {!! $formComponent->isAutofocused() ? 'autofocus' : null !!}
                    {!! $formComponent->getPlaceholder() ? 'placeholder="' . __($formComponent->getPlaceholder()) . '"' : null !!}
                    type="text"
                    x-on:keydown.enter.stop.prevent="createTag()"
                    x-model="newTag"
                    class="block w-full placeholder-gray-400 focus:placeholder-gray-500 placeholder-opacity-100 border-0"
                    x-bind:class="{ 'text-danger-700': hasError }"
                />
            @endunless

            <div
                x-show="tags.length"
                class="bg-white space-x-1 relative w-full pl-3 pr-10 py-2 text-left {{ $formComponent->isDisabled() ? 'text-gray-500' : 'border-t' }} {{ $errors->has($formComponent->getName()) ? 'border-danger-600' : 'border-gray-300' }}"
            >
                <template class="inline" x-for="tag in tags" x-bind:key="tag">
                    <button
                        @unless($formComponent->isDisabled())
                            x-on:click="deleteTag(tag)"
                        @endunless
                        type="button"
                        class="my-1 truncate max-w-full inline-flex space-x-2 items-center font-mono text-xs py-1 px-2 border border-gray-300 bg-gray-100 text-gray-800 rounded shadow-sm inline-block relative @unless($formComponent->isDisabled()) cursor-pointer transition duration-200 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 hover:bg-gray-200 transition-colors duration-200 @else cursor-default @endunless"
                    >
                        <span x-text="tag"></span>

                        @unless($formComponent->isDisabled())
                            <x-heroicon-s-x class="w-3 h-3 text-gray-500" />
                        @endunless
                    </button>
                </template>
            </div>
        </div>
    </div>
</x-forms::field-group>
