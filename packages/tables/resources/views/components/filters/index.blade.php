@props([
    'applyAction',
    'form',
])

<div {{ $attributes->class(['fi-ta-filters']) }}>
    <div class="fi-ta-filters-header">
        <h4 class="fi-ta-filters-heading">
            {{ __('filament-tables::table.filters.heading') }}
        </h4>

        <div>
            <x-filament::link
                :attributes="
                    \Filament\Support\prepare_inherited_attributes(
                        new \Illuminate\View\ComponentAttributeBag([
                            'color' => 'danger',
                            'tag' => 'button',
                            'wire:click' => 'resetTableFiltersForm',
                            'wire:loading.remove.delay.' . config('filament.livewire_loading_delay', 'default') => '',
                            'wire:target' => 'resetTableFiltersForm',
                        ])
                    )
                "
            >
                {{ __('filament-tables::table.filters.actions.reset.label') }}
            </x-filament::link>

            {{
                \Filament\Support\generate_loading_indicator_html(new \Illuminate\View\ComponentAttributeBag([
                    'wire:loading.delay.' . config('filament.livewire_loading_delay', 'default') => '',
                    'wire:target' => 'tableFilters,applyTableFilters,resetTableFiltersForm',
                ]))
            }}
        </div>
    </div>

    {{ $form }}

    @if ($applyAction->isVisible())
        <div class="fi-ta-filters-apply-action-ctn">
            {{ $applyAction }}
        </div>
    @endif
</div>
