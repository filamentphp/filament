<?php

namespace Filament\Tests\Fixtures\Clusters\WithoutSubNavigationCluster\Pages;

use Filament\Pages\Page;
use Filament\Tests\Fixtures\Clusters\WithoutSubNavigationCluster;

class ClusteredPageWithoutSubNavigation extends Page
{
    protected static ?string $cluster = WithoutSubNavigationCluster::class;

    protected static ?string $navigationLabel = 'Test Page';

    protected string $view = 'filament-panels::pages.page';
}
