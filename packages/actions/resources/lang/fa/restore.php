<?php

return [

    'single' => [

        'label' => 'بازگردانی',

        'modal' => [

            'heading' => 'بازگردانی :label',

            'actions' => [

                'restore' => [
                    'label' => 'بازگردانی',
                ],

            ],

        ],

        'notifications' => [

            'restored' => [
                'title' => 'رکورد بازگردانی شد',
            ],

        ],

    ],

    'multiple' => [

        'label' => 'بازگردانی انتخاب شده',

        'modal' => [

            'heading' => 'بازگردانی :label انتخاب شده',

            'actions' => [

                'restore' => [
                    'label' => 'بازگردانی',
                ],

            ],

        ],

        'notifications' => [

            'restored' => [
                'title' => 'رکوردها بازگردانی شدند',
            ],

            'restored_partial' => [
                'title' => ':count از :total بازگردانی شد',
                'missing_authorization_failure_message' => 'شما اجازه بازگردانی :count را ندارید.',
                'missing_processing_failure_message' => ':count نتوانست بازگردانی شود.',
            ],

            'restored_none' => [
                'title' => 'بازگردانی نشد',
                'missing_authorization_failure_message' => 'شما اجازه بازگردانی :count را ندارید.',
                'missing_processing_failure_message' => ':count نتوانست بازگردانی شود.',
            ],
        ],

    ],

];
