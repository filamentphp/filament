<?php

return [

    'single' => [

        'label' => 'Барқарор кардан',

        'modal' => [

            'heading' => 'Барқарор кардани :label',

            'actions' => [

                'restore' => [
                    'label' => 'Барқарор кардан',
                ],

            ],

        ],

        'notifications' => [

            'restored' => [
                'title' => 'Сабт барқарор карда шуд',
            ],

        ],

    ],

    'multiple' => [

        'label' => 'Барқарор кардани интихобшуда',

        'modal' => [

            'heading' => 'Барқарор кардани :label интихобшуда',

            'actions' => [

                'restore' => [
                    'label' => 'Барқарор кардан',
                ],

            ],

        ],

        'notifications' => [

            'restored' => [
                'title' => 'Сабтҳо барқарор карда шуданд',
            ],

            'restored_partial' => [
                'title' => ':count аз :total барқарор карда шуд',
                'missing_authorization_failure_message' => 'Шумо барои барқарор кардани :count иҷозат надоред.',
                'missing_processing_failure_message' => 'Барқарор кардани :count муваффақ нашуд.',
            ],

            'restored_none' => [
                'title' => 'Барқарор кардан муваффақ нашуд',
                'missing_authorization_failure_message' => 'Шумо барои барқарор кардани :count иҷозат надоред.',
                'missing_processing_failure_message' => 'Барқарор кардани :count муваффақ нашуд.',
            ],

        ],

    ],

];
