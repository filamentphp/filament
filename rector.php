<?php

use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/docs-assets/app/app',
        __DIR__ . '/docs-assets/app/config',
        __DIR__ . '/docs-assets/app/database',
        __DIR__ . '/docs-assets/app/routes',
        __DIR__ . '/docs-assets/app/tests',
        __DIR__ . '/packages',
        __DIR__ . '/tests',
    ])
    ->withTypeCoverageLevel(0)
    ->withDeadCodeLevel(0)
    ->withCodeQualityLevel(0);
