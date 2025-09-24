@php
    use Filament\Forms\Components\TableSelect\Livewire\TableSelectLivewireComponent;

    $fieldWrapperView = $getFieldWrapperView();
    $extraAttributes = $getExtraAttributes();
    $id = $getId();
    $isDisabled = $isDisabled();
    $isMultiple = $isMultiple();
    $hasBadges = $hasBadges();
    $badgeColor = $getBadgeColor();
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
        @if (((! $isMultiple) && filled($optionLabel = $getOptionLabel())) ||
             ($isMultiple && filled($optionLabels = $getOptionLabels())))
            @if ($isMultiple && $hasBadges)
                <div class="fi-fo-modal-table-select-badges-ctn">
                    @foreach ($optionLabels as $optionLabel)
                        @if ($hasBadges)
                            <x-filament::badge :color="$badgeColor">
                                {{ $optionLabel }}
                            </x-filament::badge>
                        @else
                            {{ $optionLabel }}
                        @endif
                    @endforeach
                </div>
            @else
                <div>
                    @if ($hasBadges)
                        <x-filament::badge :color="$badgeColor">
                            {{ $optionLabel }}
                        </x-filament::badge>
                    @elseif ($isMultiple)
                        @foreach ($optionLabels as $optionLabel)
                            {{ $optionLabel . ($loop->last ? '' : ', ') }}
                        @endforeach
                    @else
                        {{ $optionLabel }}
                    @endif
                </div>
            @endif
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
    </div>
</x-dynamic-component>
