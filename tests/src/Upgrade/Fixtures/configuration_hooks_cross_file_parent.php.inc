<?php

namespace Filament\Tests\Upgrade\Fixtures\ConfigurationHooksCrossFile;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Pages\Page;
use Filament\Resources\Resource;

class ParentStats extends StatsOverviewWidget
{
    protected function getCards(): array
    {
        return [];
    }
}

class ParentAlignment extends Page
{
    public static function configureAlignment(): void
    {
        parent::alignFormActionsLeft();
        self::alignFormActionsRight();
        static::alignFormActionsLeft();
    }
}

class ExternallyCalledLabel extends Resource
{
    public static function getLabel(): ?string
    {
        return 'Product';
    }
}
