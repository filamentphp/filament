<?php

namespace Filament\Pages\Dashboard\Actions;

use Exception;
use Filament\Actions\Action;
use Filament\Pages\Dashboard;
use Filament\Support\Facades\FilamentIcon;
use Livewire\Component;

class FilterAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'filter';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament-panels::pages/dashboard.actions.filter.label'));

        $this->modalHeading(fn (): string => __('filament-panels::pages/dashboard.actions.filter.modal.heading'));

        $this->modalSubmitActionLabel(__('filament-panels::pages/dashboard.actions.filter.modal.actions.apply.label'));

        $this->icon(FilamentIcon::resolve('panels::pages.dashboard.actions.filter') ?? 'heroicon-m-funnel');

        $this->color('gray');

        $this->fillForm(function (Component $livewire): ?array {
            if (! property_exists($livewire, 'filters')) {
                throw new Exception('The [' . $livewire::class . '] page must implement the [' . Dashboard\Concerns\HasFilters::class . '] trait.');
            }

            return $livewire->filters;
        });

        $this->action(function (array $data, Component $livewire) {
            if (! property_exists($livewire, 'filters')) {
                return;
            }

            $livewire->filters = $data;

            $this->success();
        });

        $this->slideOver();

        $this->modalWidth('md');
    }
}
