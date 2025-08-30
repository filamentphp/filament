<?php

namespace Filament\Pages\Dashboard\Actions;

use Filament\Actions\Action;
use Filament\Pages\Dashboard;
use Filament\Support\Facades\FilamentIcon;
use Filament\Support\Icons\Heroicon;
use Filament\View\PanelsIconAlias;
use Livewire\Component;
use LogicException;

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

        $this->icon(FilamentIcon::resolve(PanelsIconAlias::PAGES_DASHBOARD_ACTIONS_FILTER) ?? Heroicon::Funnel);

        $this->defaultColor('gray');

        $this->fillForm(function (Component $livewire): ?array {
            if (! property_exists($livewire, 'filters')) {
                throw new LogicException('The [' . $livewire::class . '] page must implement the [' . Dashboard\Concerns\HasFilters::class . '] trait.');
            }

            return $livewire->filters;
        });

        $this->action(function (array $data, Component $livewire): void {
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
