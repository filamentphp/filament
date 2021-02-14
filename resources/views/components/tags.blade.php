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

@pushonce('js:tags-component')
    <script>
        function tags(config) {
            return {
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
                    if (this.value !== '' && this.value !== null) this.tags = this.value.trim().split(this.separator).filter(tag => tag !== '')

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
        separator: '{{ $separator }}',
        @if (Str::of($nameAttribute)->startsWith('wire:model')) value: @entangle($name){{ Str::of($nameAttribute)->after('wire:model') }}, @endif
    })"
    x-init="init()"
    {{ $attributes->merge($extraAttributes) }}
>
    @unless (Str::of($nameAttribute)->startsWith(['wire:model', 'x-model']))
        <input
            x-model="value"
            @if ($name) {{ $nameAttribute }}="{{ $name }}" @endif
            type="hidden"
        />
    @endif

    <div x-show="tags.length || {{ $disabled ? 'false' : 'true' }}" class="rounded shadow-sm border overflow-hidden {{ $errors->has($errorKey) ? 'border-red-600 motion-safe:animate-shake' : 'border-gray-300' }}">
        @unless ($disabled)
            <input
                autocomplete="off"
                @if ($autofocus) autofocus @endif
                placeholder="{{ $placeholder }}"
                type="text"
                x-on:keydown.enter.stop.prevent="createTag()"
                x-model="newTag"
                x-ref="newTag"
                class="block w-full placeholder-gray-400 focus:placeholder-gray-500 placeholder-opacity-100 focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 border-0"
            />
        @endunless

        <div
            x-show="tags.length"
            class="bg-white relative w-full pl-3 pr-10 py-2 text-left {{ $disabled ? 'text-gray-500' : 'border-t' }} {{ $errors->has($errorKey) ? 'border-red-600' : 'border-gray-300' }}"
        >
            <template x-for="(tag, index) in tags" x-bind:key="tag">
                <button
                    @unless($disabled) x-on:click="deleteTag(index)" @endunless
                    type="button"
                    class="my-1 truncate max-w-full inline-flex space-x-2 items-center font-mono text-xs py-1 px-2 border border-gray-300 bg-gray-100 text-gray-800 rounded shadow-sm inline-block relative @unless($disabled) cursor-pointer transition duration-200 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 hover:bg-gray-200 transition-colors duration-200 @else cursor-default @endunless"
                >
                    <span x-text="tag"></span>

                    @unless($disabled)
                        <x-heroicon-s-x class="h-3 w-3 text-gray-500" />
                    @endunless
                </button>
            </template>
        </div>
    </div>
</div>
