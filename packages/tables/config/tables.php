<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Date / Time Formatting
    |--------------------------------------------------------------------------
    |
    | These are the formats that Filament will use to display dates and times
    | by default.
    |
    */

    'date_format' => 'M j, Y',
    'date_time_format' => 'M j, Y H:i:s',
    'time_format' => 'H:i:s',

    /*
    |--------------------------------------------------------------------------
    | Default Currency
    |--------------------------------------------------------------------------
    |
    | This is the currency that Filament will use to display amounts
    | by default.
    |
    */

    'default_currency' => env('DEFAULT_CURRENCY', 'USD'),

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | This is the storage disk Filament will use to find media. You may use any
    | of the disks defined in the `config/filesystems.php`.
    |
    */

    'default_filesystem_disk' => env('TABLES_FILESYSTEM_DRIVER', 'public'),

    /*
    |--------------------------------------------------------------------------
    | Dark mode
    |--------------------------------------------------------------------------
    |
    | By enabling this setting, your tables will be ready for Tailwind's Dark
    | Mode feature.
    |
    | https://tailwindcss.com/docs/dark-mode
    |
    */

    'dark_mode' => false,

    /*
    |--------------------------------------------------------------------------
    | Pagination
    |--------------------------------------------------------------------------
    |
    | This is the configuration for the pagination of tables.
    |
    */

    'pagination' => [
        'default_records_per_page' => 10,
        'records_per_page_select_options' => [5, 10, 25, 50, -1],
    ],

    /*
    |--------------------------------------------------------------------------
    | Layout
    |--------------------------------------------------------------------------
    |
    | This is the configuration for the general layout of tables.
    |
    */

    'layout' => [
        'actions' => [
            'cell' => [
                'alignment' => 'right',
            ],
            'modal' => [
                'actions' => [
                    'alignment' => 'left',
                ],
            ],
        ],
    ],

];
