@props([
    'table',
])

@php
    $isLoaded = $table->isLoaded();
    $records = $isLoaded ? $table->getRecords() : null;
    $isSelectionEnabled = $table->isSelectionEnabled() && (! ($table->isGroupsOnly() && $table->getGrouping()));
@endphp

<div
    @if (! $isLoaded)
        wire:init="loadTable"
    @endif
    x-data="filamentTable({
                areGroupsCollapsedByDefault: @js($table->areGroupsCollapsedByDefault()),
                canTrackDeselectedRecords: @js($table->canTrackDeselectedRecords()),
                currentSelectionLivewireProperty: @js($table->getCurrentSelectionLivewireProperty()),
                maxSelectableRecords: @js($table->getMaxSelectableRecords()),
                selectsCurrentPageOnly: @js($table->selectsCurrentPageOnly()),
                $wire,
            })"
    {{
        $table->getExtraAttributeBag()->class([
            'fi-ta',
            'fi-loading' => $records === null,
        ])
    }}
>
    <input
        type="hidden"
        value="{{ ($isSelectionEnabled && $isLoaded) ? $table->getAllSelectableRecordsCount() : null }}"
        x-ref="allSelectableRecordsCount"
    />

    {{-- Raw PHP instead of `@if`, so Livewire does not inject `<!--[if BLOCK]-->` markers around the frame. --}}
    <?php if ($table->isContained()) { ?>
        <div
            @class([
                'fi-ta-ctn',
                'fi-ta-ctn-with-content-layout' => $table->hasContentLayout(),
                'fi-ta-ctn-with-footer' => $table->hasFooter(),
                'fi-ta-ctn-with-header' => $table->hasHeader(),
            ])
        >
            {{ $slot }}
        </div>
    <?php } else { ?>
        {{ $slot }}
    <?php } ?>

    <x-filament-actions::modals />
</div>
