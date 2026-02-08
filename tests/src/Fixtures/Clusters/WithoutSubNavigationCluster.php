<?php

namespace Filament\Tests\Fixtures\Clusters;

use Filament\Clusters\Cluster;

class WithoutSubNavigationCluster extends Cluster
{
    protected static bool $shouldRegisterSubNavigation = false;

    protected static ?string $navigationLabel = 'Cluster Without Sub Navigation';
}
