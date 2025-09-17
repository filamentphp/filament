@php
    use Filament\Forms\Components\TableSelect\Livewire\TableSelectLivewireComponent;

    $fieldWrapperView = $getFieldWrapperView();
    $extraAttributes = $getExtraAttributes();
    $id = $getId();
    $isDisabled = $isDisabled();
    $isMultiple = $isMultiple();
@endphp

<x-dynamic-component :component="$fieldWrapperView" :field="$field">
    <div
        {{
            $attributes
                ->merge([
                    'id' => $id,
                ], escape: false)
                ->merge($extraAttributes, escape: false)
                ->class([
                    'fi-fo-modal-table-select',
                    'fi-fo-modal-table-select-disabled' => $isDisabled,
                    'fi-fo-modal-table-select-multiple' => $isMultiple,
                ])
        }}
    >
        @if ($isMultiple)
            @if (filled($optionLabels = $getOptionLabels()))
                <div class="fi-fo-modal-table-select-badges-ctn">
                    @foreach ($optionLabels as $optionLabel)
                        <x-filament::badge>
                            {{ $optionLabel }}
                        </x-filament::badge>
                    @endforeach
                </div>
            @elseif (filled($placeholder = $getPlaceholder()))
                <div class="fi-fo-modal-table-select-placeholder">
                    {{ $placeholder }}
                </div>
            @endif

            @if (! $isDisabled)
                <div>
                    {{ $getAction('select') }}
                </div>
            @endif
        @else
            @if (filled($optionLabel = $getOptionLabel()))
                {{ $optionLabel }}
            @elseif (filled($placeholder = $getPlaceholder()))
                <div class="fi-fo-modal-table-select-placeholder">
                    {{ $placeholder }}
                </div>
            @endif

            @if (! $isDisabled)
                {{ $getAction('select') }}
            @endif
        @endif
    </div>
</x-dynamic-component>
