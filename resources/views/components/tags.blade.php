@props([
    'autofocus' => false,
    'disabled' => false,
    'errorKey' => null,
    'extraAttributes' => [],
    'name',
    'nameAttribute' => 'name',
    'placeholder' => null,
    'required' => false,
    'separator' => ',',
])

@pushonce('js:tags')
    <script>
        function tags(config) {
            return {
                name: config.name,

                newTag: '',

                separator: config.separator,

                tags: [],

                value: config.value,

                createTag: function () {
                    this.newTag = this.newTag.trim()

                    if (this.newTag === '') return

                    if (this.tags.includes(this.newTag)) return this.newTag = ''

                    this.tags.push(this.newTag)

                    this.newTag = ''
                },

                deleteTag: function (index) {
                    this.tags.splice(index, 1)
                },

                init: function () {
                    if (this.value !== '') this.tags = this.value.split(this.separator)

                    this.$watch('tags', ((tags) => {
                        this.value = tags.join(this.separator)
                    }))
                },
            }
        }
    </script>
@endpushonce

<div
    x-data="tags({
        autofocus: {{ $autofocus ? 'true' : 'false' }},
        name: '{{ $name }}',
        separator: '{{ $separator }}',
        @if (Str::of($nameAttribute)->startsWith('wire:model')) value: @entangle($name){{ Str::of($nameAttribute)->after('wire:model') }}, @endif
    })"
    x-init="init()"
    {{ $attributes->merge(array_merge([
        'class' => 'relative',
    ], $extraAttributes)) }}
>
    @unless (Str::of($nameAttribute)->startsWith(['wire:model', 'x-model']))
        <input
            x-model="value"
            @if ($name) {{ $nameAttribute }}="{{ $name }}" @endif
            type="hidden"
        />
    @endif

    <div
        class="bg-white relative w-full border border-gray-300 rounded shadow-sm pl-3 pr-10 py-2 text-left cursor-default focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 {{ $disabled ? 'text-gray-500' : '' }}"
    >
        <template x-for="(tag, index) in tags" x-bind:key="tag">
            <button
                x-on:click="deleteTag(index)"
                type="button"
                class="cursor-pointer my-1 truncate max-w-full inline-flex space-x-2 items-center font-semibold text-xs py-1 px-3 border border-gray-300 bg-gray-100 text-gray-800 rounded transition duration-200 shadow-sm inline-block relative focus:ring focus:ring-indigo-200 focus:ring-opacity-50 hover:bg-gray-200"
            >
                <span x-text="tag"></span>

                <x-heroicon-s-x class="h-4 w-4 text-gray-500" />
            </button>
        </template>

        @unless($disabled)
            <button
                type="button"
                class="cursor-pointer my-1 inline-flex space-x-2 items-center py-1 px-3 border border-gray-300 bg-gray-100 text-gray-800 rounded shadow-sm inline-block relative"
            >
                <input
                    autocomplete="off"
                    @if ($autofocus) autofocus @endif
                    placeholder="{{ $placeholder }}"
                    type="text"
                    x-on:keydown.enter.prevent="createTag()"
                    x-model="newTag"
                    x-ref="newTag"
                    class="text-xs font-semibold bg-gray-100 border-0 p-0 focus:ring-0 focus:outline-none"
                />
            </button>
        @endunless
    </div>
</div>
