<?php

use Filament\Upgrade\Rector;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->rules([
        Rector\SimpleMethodChangesRector::class,
    ]);
};
