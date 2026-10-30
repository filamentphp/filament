<?php

namespace Filament\Tests\Upgrade\Fixtures\ConfigurationHooksManual;

use Filament\Pages\Page;
use Filament\Resources\Resource;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\Widget;

class BothNames extends Page
{
    protected function getActions(): array { return []; }
    protected function getHeaderActions(): array { return []; }
}

class BothLabels extends Resource
{
    protected static ?string $label = 'old';
    protected static ?string $modelLabel = '';
    protected static ?string $pluralLabel = 'older';
    protected static ?string $pluralModelLabel = '';
}

class WrittenFilters extends Widget
{
    use InteractsWithPageFilters;

    public function report(): array
    {
        $this->filters['period'] = 'custom';

        return $this->filters;
    }
}

class RealFilters extends Widget
{
    use InteractsWithPageFilters;

    public array $filters = [];

    public function report(): array { return $this->filters; }
}

class CustomNames
{
    protected static ?string $label = 'Custom';

    protected function getActions(): array { return []; }
    protected function getCards(): array { return []; }
}
