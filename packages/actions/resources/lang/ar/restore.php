<?php

return [

    'single' => [

        'label' => 'استعادة',

        'modal' => [

            'heading' => 'استعادة :label',

            'actions' => [

                'restore' => [
                    'label' => 'استعادة',
                ],

            ],

        ],

        'notifications' => [

            'restored' => [
                'title' => 'تمت الاستعادة',
            ],

        ],

    ],

    'multiple' => [

        'label' => 'استعادة المحدد',

        'modal' => [

            'heading' => 'استعادة :label',

            'actions' => [

                'restore' => [
                    'label' => 'استعادة',
                ],

            ],

        ],

        'notifications' => [

            'restored' => [
                'title' => 'تمت الاستعادة',
            ],

            'restored_partial' => [
                'title' => 'تمت استعادة :count من أصل :total',
                'missing_authorization_failure_message' => 'ليس لديك صلاحية لاستعادة :count.',
                'missing_processing_failure_message' => 'تعذر استعادة :count.',
            ],

            'restored_none' => [
                'title' => 'فشل في الاستعادة',
                'missing_authorization_failure_message' => 'ليس لديك صلاحية لاستعادة :count.',
                'missing_processing_failure_message' => 'تعذر استعادة :count.',
            ],

        ],

    ],

];
