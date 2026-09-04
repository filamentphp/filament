<?php

return [

    'single' => [

        'label' => 'Нест кардан',

        'modal' => [

            'heading' => 'Нест кардани :label',

            'actions' => [

                'delete' => [
                    'label' => 'Нест кардан',
                ],

            ],

        ],

        'notifications' => [

            'deleted' => [
                'title' => 'Нест карда шуд',
            ],

        ],

    ],

    'multiple' => [

        'label' => 'Нест кардани интихобшуда',

        'modal' => [

            'heading' => 'Нест кардани :label интихобшуда',

            'actions' => [

                'delete' => [
                    'label' => 'Нест кардани интихобшуда',
                ],

            ],

        ],

        'notifications' => [

            'deleted' => [
                'title' => 'Нест карда шуд',
            ],

            'deleted_partial' => [
                'title' => ':count аз :total нест карда шуд',
                'missing_authorization_failure_message' => 'Шумо барои нест кардани :count иҷозат надоред.',
                'missing_processing_failure_message' => ':count-ро нест кардан имконнопазир аст.',
            ],

            'deleted_none' => [
                'title' => 'Нест кардан муваффақ нашуд',
                'missing_authorization_failure_message' => 'Шумо барои нест кардани :count иҷозат надоред.',
                'missing_processing_failure_message' => ':count-ро нест кардан имконнопазир аст.',
            ],

        ],

    ],

];
