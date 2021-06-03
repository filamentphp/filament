<?php

return [

    'default_filesystem_disk' => env('FILAMENT_FILESYSTEM_DRIVER', 'public'),

    'default_attachment_upload_route' => null,

    'first_day_of_week' => 1, // 0 to 7 are accepted values, with Monday as 1 and Sunday as 7 or 0.

];
