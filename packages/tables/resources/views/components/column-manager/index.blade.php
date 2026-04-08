@php
    use Filament\Tables\Enums\ColumnManagerResetActionPosition;
    use Illuminate\View\ComponentAttributeBag;
@endphp

@props([
    'applyAction',
    'columns' => null,
    'hasReorderableColumns',
    'hasToggleableColumns',
    'headingTag' => 'h3',
    'reorderAnimationDuration' => 300,
    'resetActionPosition' => ColumnManagerResetActionPosition::Header,
])

<div
    x-data="filamentTableColumnManager({
                columns: $wire.entangle('tableColumns'),
                isLive: {{ $applyAction->isVisible() ? 'false' : 'true' }},
            })"
    class="fi-ta-col-manager"
>
    <div class="fi-ta-col-manager-header">
        <{{ $headingTag }} class="fi-ta-col-manager-heading">
            {{ __('filament-tables::table.column_manager.heading') }}
        </{{ $headingTag }}>

        @if ($resetActionPosition === ColumnManagerResetActionPosition::Header)
            <div>
                <x-filament::link
                    :attributes="
                        \Filament\Support\prepare_inherited_attributes(
                            new ComponentAttributeBag([
                                'color' => 'danger',
                                'tag' => 'button',
                                'wire:click' => 'resetTableColumnManager',
                                'wire:loading.remove.delay.' . config('filament.livewire_loading_delay', 'default') => '',
                                'wire:target' => 'resetTableColumnManager',
                                'x-on:click' => 'resetDeferredColumns',
                            ])
                        )
                    "
                >
                    {{ __('filament-tables::table.column_manager.actions.reset.label') }}
                </x-filament::link>
            </div>
        @endif
    </div>

    <x-filament-tables::column-manager.content
        :columns="$columns"
        :has-reorderable-columns="$hasReorderableColumns"
        :has-toggleable-columns="$hasToggleableColumns"
        :reorder-animation-duration="$reorderAnimationDuration"
    />

    @if ($applyAction->isVisible() || $resetActionPosition === ColumnManagerResetActionPosition::Footer)
        <div class="fi-ta-col-manager-actions-ctn">
            @if ($applyAction->isVisible())
                {{ $applyAction }}
            @endif

            @if ($resetActionPosition === ColumnManagerResetActionPosition::Footer)
                <x-filament::button
                    color="danger"
                    wire:click="resetTableColumnManager"
                    x-on:click="resetDeferredColumns"
                >
                    {{ __('filament-tables::table.column_manager.actions.reset.label') }}
                </x-filament::button>
            @endif
        </div>
    @endif
</div>
