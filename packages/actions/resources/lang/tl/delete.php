<?php

return [

    'single' => [

        'label' => 'Burahin',

        'modal' => [

            'heading' => 'Burahin ang :label',

            'actions' => [

                'delete' => [
                    'label' => 'Burahin',
                ],

            ],

        ],

        'notifications' => [

            'deleted' => [
                'title' => 'Nabura',
            ],

        ],

    ],

    'multiple' => [

        'label' => 'Burahin ang napili',

        'modal' => [

            'heading' => 'Burahin ang napiling :label',

            'actions' => [

                'delete' => [
                    'label' => 'Burahin',
                ],

            ],

        ],

        'notifications' => [

            'deleted' => [
                'title' => 'Nabura',
            ],

            'deleted_partial' => [
                'title' => 'Nabura ang :count sa :total',
                'missing_authorization_failure_message' => 'Wala kang pahintulot na burahin ang :count.',
                'missing_processing_failure_message' => 'Hindi mabura ang :count.',
            ],

            'deleted_none' => [
                'title' => 'Hindi nabura',
                'missing_authorization_failure_message' => 'Wala kang pahintulot na burahin ang :count.',
                'missing_processing_failure_message' => 'Hindi mabura ang :count.',
            ],

        ],

    ],

];
