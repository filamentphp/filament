<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Automatic Eager Loading
    |--------------------------------------------------------------------------
    |
    | When enabled, Filament will automatically detect relationships used in
    | table columns and eager load them to prevent N+1 query issues.
    |
    | This feature analyzes column names (e.g., 'author.name') and automatically
    | applies ->with() to the query builder.
    |
    */

    'auto_eager_load' => env('FILAMENT_AUTO_EAGER_LOAD', true),

    /*
    |--------------------------------------------------------------------------
    | N+1 Query Detection
    |--------------------------------------------------------------------------
    |
    | Enable detection of potential N+1 query issues in development environments.
    | When the query threshold is exceeded, warnings will be logged and optionally
    | displayed to developers.
    |
    | Only active in 'local' and 'testing' environments.
    |
    */

    'enable_n1_detection' => env('FILAMENT_N1_DETECTION', true),

    'n1_query_threshold' => env('FILAMENT_N1_THRESHOLD', 20),

    /*
    |--------------------------------------------------------------------------
    | Performance Warnings
    |--------------------------------------------------------------------------
    |
    | Display performance warnings to developers during local development.
    | Warnings include query counts, potential N+1 issues, and optimization
    | suggestions.
    |
    */

    'enable_warnings' => env('FILAMENT_PERFORMANCE_WARNINGS', true),

    'log_eager_loading' => env('FILAMENT_LOG_EAGER_LOADING', false),

    /*
    |--------------------------------------------------------------------------
    | Query Caching
    |--------------------------------------------------------------------------
    |
    | Configure default settings for table query caching. Caching can
    | significantly reduce database load for frequently accessed data.
    |
    | Note: Caching is opt-in per table/resource.
    |
    */

    'query_cache' => [
        'default_ttl' => env('FILAMENT_QUERY_CACHE_TTL', 60), // seconds
        'enabled' => env('FILAMENT_QUERY_CACHE_ENABLED', false),
        'cache_driver' => env('FILAMENT_QUERY_CACHE_DRIVER', null), // null = default cache driver
    ],

    /*
    |--------------------------------------------------------------------------
    | Performance Profiling
    |--------------------------------------------------------------------------
    |
    | Enable performance profiling to track query counts, execution times,
    | and memory usage. Use 'php artisan filament:profile' to generate reports.
    |
    */

    'profiling' => [
        'enabled' => env('FILAMENT_PROFILING_ENABLED', true),
        'track_queries' => env('FILAMENT_PROFILING_TRACK_QUERIES', true),
        'track_memory' => env('FILAMENT_PROFILING_TRACK_MEMORY', true),
    ],

];
