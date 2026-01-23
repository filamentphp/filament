<?php

namespace Filament\Tests\Fixtures\Clusters\NoSubNavigationCluster\Pages;

use Filament\Pages\Page;
use Filament\Tests\Fixtures\Clusters\ClusterWithoutSubNavigation;

class ClusteredPageWithoutSubNavigation extends Page
{
    protected static ?string $cluster = ClusterWithoutSubNavigation::class;

    protected static ?string $navigationLabel = 'Test Page';

    protected string $view = 'filament-panels::pages.page';
}
