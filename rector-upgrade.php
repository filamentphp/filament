<?php

use Filament\Upgrade\Rector\RenameDeprecatedForwardingMethodsRector;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->sets([__DIR__ . '/packages/upgrade/src/rector.php']);
    $rectorConfig->cacheDirectory(__DIR__ . '/.rector.cache/upgrade');
    $rectorConfig->paths([
        __DIR__ . '/docs-assets/app/app',
        __DIR__ . '/docs-assets/app/config',
        __DIR__ . '/docs-assets/app/database',
        __DIR__ . '/docs-assets/app/routes',
        __DIR__ . '/docs-assets/app/tests',
        __DIR__ . '/packages',
        __DIR__ . '/tests',
    ]);
    $rectorConfig->autoloadPaths([
        __DIR__ . '/tests/src',
        __DIR__ . '/docs-assets/app/app',
    ]);
    $rectorConfig->skip([
        __DIR__ . '/tests/src/Upgrade/Fixtures',
        // Keep deliberate coverage of the supported legacy relationship accessors.
        RenameDeprecatedForwardingMethodsRector::class => [
            __DIR__ . '/tests/src/Tables/Filters/SelectFilterTest.php',
        ],
    ]);

    // Keep this check separate from formatting and preserve our import style.
    $rectorConfig->importNames(false, false);
};
