<?php

namespace Filament\Tests\Fixtures\Clusters\NoSubNavigationCluster\Pages;

use Filament\Pages\Page;
use Filament\Tests\Fixtures\Clusters\NoSubNavigationCluster;

class TestPage extends Page
{
    protected static ?string $cluster = NoSubNavigationCluster::class;

    protected static ?string $navigationLabel = 'Test Page';

    protected string $view = 'filament-panels::pages.page';
}
