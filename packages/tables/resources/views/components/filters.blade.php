@props([
    'applyAction',
    'form',
    'headingTag' => 'h3',
])

<div {{ $attributes->class(['fi-ta-filters']) }}>
    <div class="fi-ta-filters-header">
        <{{ $headingTag }} class="fi-ta-filters-heading">
            {{ __('filament-tables::table.filters.heading') }}
        </{{ $headingTag }}>

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
        </div>
    </div>

    {{ $form }}

    @if ($applyAction->isVisible())
        <div class="fi-ta-filters-apply-action-ctn">
            {{ $applyAction }}
        </div>
    @endif
</div>
