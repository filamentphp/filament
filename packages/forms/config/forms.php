<?php

return [

    'default_filesystem_disk' => env('FILAMENT_FILESYSTEM_DRIVER', 'public'),

    'default_attachment_upload_route' => null,

    'first-day-of-week' => 1, //0 to 7 are accepted values. Monday = 1, Sunday = 7 or 0.

];
