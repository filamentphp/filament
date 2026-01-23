<?php

namespace Filament\Tests\Fixtures\Clusters;

use Filament\Clusters\Cluster;

class ClusterWithoutSubNavigation extends Cluster
{
    protected static bool $shouldRegisterSubNavigation = false;

    protected static ?string $navigationLabel = 'No Sub Navigation';
}
