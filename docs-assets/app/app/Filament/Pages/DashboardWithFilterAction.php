<?php

namespace App\Filament\Pages;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Pages\Dashboard\Actions\FilterAction;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Pages\Dashboard\Concerns\HasFiltersAction;

class DashboardWithFilterAction extends BaseDashboard
{
    use HasFiltersAction;

    protected static string $routePath = 'dashboard-filter-action';

    protected static ?string $title = 'Dashboard';

    protected static bool $shouldRegisterNavigation = false;

    protected function getHeaderActions(): array
    {
        return [
            FilterAction::make()
                ->schema([
                    DatePicker::make('startDate')
                        ->label('Start date')
                        ->columnSpanFull(),
                    DatePicker::make('endDate')
                        ->label('End date')
                        ->columnSpanFull(),
                    Select::make('status')
                        ->options([
                            'all' => 'All statuses',
                            'active' => 'Active',
                            'inactive' => 'Inactive',
                        ])
                        ->columnSpanFull(),
                ]),
        ];
    }
}
