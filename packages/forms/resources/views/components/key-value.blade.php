@pushonce('filament-scripts:key-value-component')
    <script>
        function keyValue(config) {
            return {
                canDeleteRows: config.canDeleteRows,
                rows: [{key: 'test', value: 'hey'}, {key: 'test', value: 'hey'}]
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
        canDeleteRows: {{ json_encode($formComponent->canDeleteRows) }}
    })"
        {!! Filament\format_attributes($formComponent->extraAttributes) !!}
    >
        <div class="overflow-x-auto bg-white rounded">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-200">
                    <tr>
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
                            <th scope="col">
                                <span class="sr-only">{{ __($formComponent->deleteButtonLabel) }}</span>
                            </th>
                        @endif
                    </tr>
                </thead>
                <tbody class="text-sm leading-tight divide-y divide-gray-200">
                    <template x-for="(row, index) in rows" :key="index">
                        <tr
                            x-bind:class="{ 'bg-gray-50': index % 2 }"
                        >
                            <td class="px-6 whitespace-nowrap" x-text="row.key">
                                <div class="py-4" x-text="row.key"></div>
                            </td>
                            <td class="px-6 whitespace-nowrap">
                                <div x-text="row.value" class="py-4"></div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>
</x-forms::field-group>
