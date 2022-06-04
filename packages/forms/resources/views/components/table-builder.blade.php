@php
$deleteRows = $canDeleteRows()
@endphp

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
        x-data="{
            state: $wire.{{ $applyStateBindingModifiers('entangle(\'' . $getStatePath() . '\')') }},
            columns: 1,
            init() {
                this.columns = this.state.reduce((maxLength, currentItem) => {
                    return currentItem.length > maxLength ? currentItem.length : maxLength;
                }, 1)
            },
            addRow() {
                this.state.push(Array(this.columns).fill(null))
            },
            addColumn() {
                this.columns++

                for (let column = 0; column < this.columns; column++) {
                    for (const i in this.state) {
                        if (this.state[i][column] !== undefined) {
                            continue
                        }

                        this.state[i][column] = null
                    }
                }
            },
            removeRow(row) {
                this.state = this.state.filter((_, i) => i !== row)
            },
            removeColumn(column) {
                this.columns--;

                this.state = this.state.map(row => {
                    return row.filter((_, i) => i !== (column - 1))
                })
            }
        }"
        {{ $attributes->merge($getExtraAttributes())->class(['filament-forms-table-builder-component']) }}
        {{ $getExtraAlpineAttributeBag() }}
    >
        <div @class([
            'border border-gray-300 divide-y shadow-sm bg-white rounded-xl overflow-hidden',
            'dark:bg-gray-700 dark:border-gray-600 dark:divide-gray-600' => config('forms.dark_mode'),
        ])>
            <table @class([
                'w-full text-left rtl:text-right divide-y table-auto',
                'dark:divide-gray-700' => config('forms.dark_mode'),
            ])>
                <thead>
                    <tr @class([
                        'bg-gray-50 divide-x',
                        'dark:bg-gray-800/60' => config('forms.dark_mode'),
                    ])>
                        <template x-for="i in columns">
                            <th @class([
                                'px-4 py-2 whitespace-nowrap font-medium text-sm text-gray-600',
                                'dark:text-gray-300' => config('forms.dark_mode'),
                            ]) scope="col">
                                <div class="flex items-center justify-between">
                                    <span x-text="i"></span>

                                    <button
                                        x-on:click="removeColumn(i)"
                                        x-show="columns > 1"
                                        type="button"
                                        class="text-gray-400 hover:text-gray-600 focus:text-gray-600"
                                    >
                                        <x-heroicon-o-x class="w-4 h-4" />
                                    </button>
                                </div>
                            </th>
                        </template>

                        @if($deleteRows)
                            <th x-show="state.length > 1" class="w-10">

                            </th>
                        @endif
                    </tr>
                </thead>

                <tbody @class([
                    'divide-y whitespace-nowrap',
                    'dark:divide-gray-600' => config('forms.dark_mode'),
                ])>
                    <template x-for="(row, index) in state" x-bind:key="index">
                        <tr @class([
                            'divide-x',
                            'dark:divide-gray-600' => config('forms.dark_mode'),
                        ])>
                            <template x-for="column in columns">
                                <td>
                                    <input
                                        type="text"
                                        x-model="row[column - 1]"
                                        class="w-full px-4 py-3 font-mono text-sm bg-transparent border-0 focus:ring-0"
                                    >
                                </td>
                            </template>

                            @if($deleteRows)
                                <td x-show="state.length > 1" class="p-0">
                                    <div class="flex items-center justify-center">
                                        <button
                                            x-on:click="removeRow(index)"
                                            type="button"
                                            class="text-gray-400 hover:text-gray-600 focus:text-gray-600"
                                        >
                                            <x-heroicon-o-x class="w-4 h-4" />
                                        </button>
                                    </div>
                                </td>
                            @endif
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        <div class="mt-4 space-x-4">
            @if($canAddRows())
                <x-forms::button x-on:click="addRow" color="secondary" size="sm">
                    Add Row
                </x-forms::button>
            @endif

            <x-forms::button x-on:click="addColumn" color="secondary" size="sm">
                Add Column
            </x-forms::button>
        </div>
    </div>
</x-dynamic-component>
