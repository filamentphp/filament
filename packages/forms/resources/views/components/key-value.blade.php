@pushonce('filament-scripts:key-value-component')
    <script>
        function keyValue(config) {
            return {
                canDeleteRows: config.canDeleteRows,

                name: config.name,

                rows: [{ key: null, value: null }],

                init: function () {
                    const originalValue = this.$wire.get(this.name) || {}

                    if (originalValue && Object.keys(originalValue).length > 0) {
                        this.rows = []

                        Object.entries(originalValue).forEach(([key, value]) => {
                            this.rows.push({ key, value })
                        })
                    }
                },

                addRow: function () {
                    this.rows.push({ key: '', value: '' })
                },

                updateKey: function (index, key) {
                    this.rows[index].key = key

                    this.updateLivewire()
                },

                updateValue: function (index, value) {
                    this.rows[index].value = value

                    this.updateLivewire()
                },

                deleteRow: function (index) {
                    this.rows.splice(index, 1)

                    if (this.rows.length <= 0) {
                        this.addRow()
                    }

                    this.updateLivewire()
                },

                shouldShowDeleteButton: function () {
                    if (this.rows.length > 1) return true

                    return this.canDeleteRows && this.rows.length > 0 && !!this.rows[0].key
                },

                updateLivewire: function (index = null) {
                    const rows = this.rows.reduce((accum, { key, value }) => {
                        if (! key) return accum

                        accum[key] = value

                        return accum
                    }, {})

                    this.$wire.set(`${this.name}`, rows)
                }
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
    <div x-data="keyValue({
        canDeleteRows: {{ json_encode($formComponent->canDeleteRows) }},
        name: {{ json_encode($formComponent->name) }},
        @if (Str::of($formComponent->nameAttribute)->startsWith('wire:model'))
            value: @entangle($formComponent->name){{ Str::of($formComponent->nameAttribute)->after('wire:model') }},
        @endif
    })"
        x-init="init"
        class="space-y-4"
        {!! Filament\format_attributes($formComponent->extraAttributes) !!}
    >
        <div class="overflow-x-auto bg-white rounded border border-gray-300">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-200">
                    <tr class="divide-x divide-gray-300">
                        <th class="px-6 py-3 text-left text-gray-600" scope="col">
                            <span class="text-xs font-medium tracking-wider uppercase">
                                {{ __($formComponent->keyLabel) }}
                            </span>
                        </th>
                        <th class="px-6 py-3 text-left text-gray-600" scope="col">
                            <span class="text-xs font-medium tracking-wider uppercase">
                                {{ __($formComponent->valueLabel) }}
                            </span>
                        </th>

                        @if ($formComponent->deleteButtonLabel)
                            <th scope="col" x-show="shouldShowDeleteButton()">
                                <span class="sr-only">{{ __($formComponent->deleteButtonLabel) }}</span>
                            </th>
                        @endif
                    </tr>
                </thead>
                <tbody class="text-sm leading-tight divide-y divide-gray-200">
                    <template x-for="(row, index, collection) in rows" :key="index">
                        <tr
                            x-bind:class="{ 'bg-gray-50': index % 2 }"
                        >
                            <td class="whitespace-nowrap border-r border-gray-300">
                                <input
                                    type="text"
                                    placeholder="Enter key..."
                                    class="px-6 py-4 border-0 w-full bg-transparent placeholder-gray-400 focus:placeholder-gray-500 placeholder-opacity-100 focus:border-1 focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                    x-bind:value="rows[index].key"
                                    @input.debounce.500ms="updateKey(index, $event.target.value)"
                                >
                            </td>
                            <td class="whitespace-nowrap">
                                <input
                                    type="text"
                                    placeholder="Enter value..."
                                    class="px-6 py-4 border-0 w-full bg-transparent placeholder-gray-400 focus:placeholder-gray-500 placeholder-opacity-100 focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                    x-bind:value="rows[index].value"
                                    @input.debounce.500ms="updateValue(index, $event.target.value)"
                                >
                            </td>
                            <td x-show="shouldShowDeleteButton()" class="whitespace-nowrap border-l border-gray-300">
                                <div class="flex items-center justify-center">
                                    <button
                                        type="button"
                                        @click="deleteRow(index)"
                                        title="{{ __($formComponent->deleteButtonLabel) }}"
                                        class="text-danger-600 hover:text-danger-700"
                                    >
                                        <x-heroicon-o-trash class="w-4 h-4" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        <div>
            <x-filament::button
                type="button"
                @click="addRow"
            >
                {{ __($formComponent->addButtonLabel) }}
            </x-filament::button>
        </div>
    </div>
</x-forms::field-group>
