<?php

namespace App\Filament\Pages;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class DashboardWithFilters extends BaseDashboard
{
    use HasFiltersForm;

    protected static string $routePath = 'dashboard-filtered';

    protected static ?string $title = 'Dashboard';

    protected static bool $shouldRegisterNavigation = false;

    public function filtersForm(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        DatePicker::make('startDate')
                            ->label('Start date'),
                        DatePicker::make('endDate')
                            ->label('End date'),
                        Select::make('status')
                            ->options([
                                'all' => 'All statuses',
                                'active' => 'Active',
                                'inactive' => 'Inactive',
                            ]),
                    ])
                    ->columns(3)
                    ->columnSpanFull(),
            ]);
    }
}
