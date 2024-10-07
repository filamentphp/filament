<?php

namespace Filament\Resources\Resource\Concerns;

use Filament\Clusters\Cluster;

trait BelongsToCluster
{
    /**
     * @var class-string<Cluster> | null
     */
    protected static ?string $cluster = null;

    /**
     * @return class-string<Cluster> | null
     */
    public static function getCluster(): ?string
    {
        return static::$cluster;
    }
}
