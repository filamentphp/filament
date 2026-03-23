<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Actions\Action;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class AnalyticsWithSubheading extends Page
{
    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedChartBarSquare;

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $title = 'Analytics';

    protected ?string $heading = 'Analytics';

    protected ?string $subheading = 'View your website performance metrics and visitor insights.';

    protected static ?string $slug = 'analytics-subheading';

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Page views')
                    ->description('Last 30 days')
                    ->schema([
                        View::make('filament.pages.analytics-stat')
                            ->viewData(['value' => '24,521']),
                    ]),
                Section::make('Bounce rate')
                    ->description('Last 30 days')
                    ->schema([
                        View::make('filament.pages.analytics-stat')
                            ->viewData(['value' => '32.4%']),
                    ]),
                Section::make('Avg. session duration')
                    ->description('Last 30 days')
                    ->schema([
                        View::make('filament.pages.analytics-stat')
                            ->viewData(['value' => '4m 12s']),
                    ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('export')
                ->label('Export report')
                ->icon(Heroicon::OutlinedArrowDownTray)
                ->color('gray'),
        ];
    }
}
