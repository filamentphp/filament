@pushonce('filament-scripts:tags-input-component')
    <script>
        function tagsInput(config) {
            return {
                newTag: '',

                separator: config.separator,

                tags: [],

                value: config.value,

                error: false,

                createTag: function () {
                    this.newTag = this.newTag.trim()

                    if (this.newTag === '' || this.tags.includes(this.newTag)) {
                        this.error = true

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

                    this.$watch('newTag', () => this.error = false)

                    this.$watch('tags', (() => {
                        this.value = this.tags.join(this.separator)
                    }))

                    this.$watch('value', (() => {
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
                    }))
                },
            }
        }
    </script>
@endpushonce

<x-forms::field-group
    :column-span="$formComponent->columnSpan"
    :error-key="$formComponent->name"
    :for="$formComponent->id"
    :help-message="__($formComponent->helpMessage)"
    :hint="__($formComponent->hint)"
    :label="__($formComponent->label)"
    :required="$formComponent->required"
>
    <div
        x-data="tagsInput({
            separator: '{{ $formComponent->separator }}',
            @if (Str::of($formComponent->nameAttribute)->startsWith('wire:model'))
            value: @entangle($formComponent->name){{ Str::of($formComponent->nameAttribute)->after('wire:model') }},
            @endif
            })"
        x-init="init()"
        {!! $formComponent->id ? "id=\"{$formComponent->id}\"" : null !!}
        {!! Filament\format_attributes($formComponent->extraAttributes) !!}
    >
        @unless (Str::of($formComponent->nameAttribute)->startsWith(['wire:model', 'x-model']))
            <input
                x-model="value"
                {!! $formComponent->name ? "{$formComponent->nameAttribute}=\"{$formComponent->name}\"" : null !!}
                type="hidden"
            />
        @endif

        <div x-show="tags.length || {{ $formComponent->disabled ? 'false' : 'true' }}" class="rounded shadow-sm border overflow-hidden {{ $errors->has($formComponent->name) ? 'border-danger-600 motion-safe:animate-shake' : 'border-gray-300' }}">
            @unless ($formComponent->disabled)
                <input
                    autocomplete="off"
                    {!! $formComponent->autofocus ? 'autofocus' : null !!}
                    {!! $formComponent->placeholder ? 'placeholder="'.__($formComponent->placeholder).'"' : null !!}
                    type="text"
                    x-on:keydown.enter.stop.prevent="createTag()"
                    x-model="newTag"
                    class="block w-full placeholder-gray-400 placeholder-opacity-100 border-0 focus:placeholder-gray-500 focus:border-secondary-300 focus:ring focus:ring-secondary-200 focus:ring-opacity-50"
                    :class="{ 'text-danger-700': error }"
                />
            @endunless

            <div
                x-show="tags.length"
                class="bg-white space-x-1 relative w-full pl-3 pr-10 py-2 text-left {{ $formComponent->disabled ? 'text-gray-500' : 'border-t' }} {{ $errors->has($formComponent->name) ? 'border-danger-600' : 'border-gray-300' }}"
            >
                <template class="inline" x-for="tag in tags" x-bind:key="tag">
                    <button
                        @unless($formComponent->disabled)
                        x-on:click="deleteTag(tag)"
                        @endunless
                        type="button"
                        class="my-1 truncate max-w-full inline-flex space-x-2 items-center font-mono text-xs py-1 px-2 border border-gray-300 bg-gray-100 text-gray-800 rounded shadow-sm inline-block relative @unless($formComponent->disabled) cursor-pointer transition duration-200 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 hover:bg-gray-200 transition-colors duration-200 @else cursor-default @endunless"
                    >
                        <span x-text="tag"></span>

                        @unless($formComponent->disabled)
                            <x-heroicon-s-x class="w-3 h-3 text-gray-500" />
                        @endunless
                    </button>
                </template>
            </div>
        </div>
    </div>
</x-forms::field-group>
