<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Dark mode
    |--------------------------------------------------------------------------
    |
    | By enabling this setting, your notifications will be ready for Tailwind's
    | Dark Mode feature.
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
    | This is the configuration for the general layout of notifications.
    |
    | You may set a maximum horizontal position using the max width setting,
    | corresponding to Tailwind screens from `sm` to `2xl`.
    | Leave it empty for no maximum.
    |
    | The push setting determines if new notifications will be added to the
    | `top` or `bottom` of the list.
    |
    */

    'layout' => [

        'alignment' => [
            'horizontal' => 'right',
            'vertical' => 'bottom',
        ],

        'max_width' => null,

    ],

];
