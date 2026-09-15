@props([
    'table',
])

@php
    $isLoaded = $table->isLoaded();
    $records = $isLoaded ? $table->getRecords() : null;
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
        value="{{ ($table->canSelectRecords() && $isLoaded) ? $table->getAllSelectableRecordsCount() : null }}"
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
            {{-- The frame is a flex row that places sidebar filters next to the records, so a custom layout gets the `fi-ta-main` column the default layout brings along itself. --}}
            <?php if ($table->hasCustomLayout()) { ?>
                <div class="fi-ta-main">
                    {{ $slot }}
                </div>
            <?php } else { ?>
                {{ $slot }}
            <?php } ?>
        </div>
    <?php } else { ?>
        {{ $slot }}
    <?php } ?>

    <x-filament-actions::modals />
</div>
