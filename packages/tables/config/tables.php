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
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | This is the storage disk Filament will use to put media. You may use any
    | of the disks defined in the `config/filesystems.php`.
    |
    */

    'default_filesystem_disk' => env('TABLES_FILESYSTEM_DRIVER', 'public'),

    /*
    |--------------------------------------------------------------------------
    | Dark mode
    |--------------------------------------------------------------------------
    |
    | By enabling this feature, your users are able to select between a light
    | and dark appearance for tables, via Tailwind's Dark Mode feature.
    |
    | https://tailwindcss.com/docs/dark-mode
    |
    */

    'dark_mode' => false,

    /*
    |--------------------------------------------------------------------------
    | Layout
    |--------------------------------------------------------------------------
    |
    | This is the configuration for the general layout of tables.
    |
    | The alignment of row actions can be changed, so that they are in the
    | `right`, `left` or `center` of the last cell.
    |
    */

    'layout' => [
        'action_alignment' => 'right',
    ],

];
