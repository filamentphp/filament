<?php

return [

    'single' => [

        'label' => 'Ба таври доимӣ нест кардан',

        'modal' => [

            'heading' => 'Ба таври доимӣ нест кардани :label',

            'actions' => [

                'delete' => [
                    'label' => 'Нест кардан',
                ],

            ],

        ],

        'notifications' => [

            'deleted' => [
                'title' => 'Сабт ба таври доимӣ нест карда шуд',
            ],

        ],

    ],

    'multiple' => [

        'label' => 'Ба таври доимӣ нест кардани интихобшуда',

        'modal' => [

            'heading' => 'Ба таври доимӣ нест кардани :label интихобшуда',

            'actions' => [

                'delete' => [
                    'label' => 'Нест кардан',
                ],

            ],

        ],

        'notifications' => [

            'deleted' => [
                'title' => 'Сабтҳо ба таври доимӣ нест карда шуданд',
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
