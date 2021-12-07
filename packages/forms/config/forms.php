<?php

return [

    'components' => [

        'date_time_picker' => [
            'first_day_of_week' => 1, // 0 to 7 are accepted values, with Monday as 1 and Sunday as 7 or 0.
        ],

    ],

    'default_filesystem_disk' => env('FORMS_FILESYSTEM_DRIVER', 'public'),

];
