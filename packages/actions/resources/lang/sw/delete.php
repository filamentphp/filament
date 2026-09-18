<?php

return [

    'single' => [
        'label' => 'Futa',

        'modal' => [
            'heading' => 'Futa :label',

            'actions' => [
                'delete' => [
                    'label' => 'Futa',
                ],
            ],
        ],

        'notifications' => [
            'deleted' => [
                'title' => 'Imefutwa',
            ],
        ],
    ],

    'multiple' => [
        'label' => 'Futa chaguo',

        'modal' => [
            'heading' => 'Futa chaguo :label',

            'actions' => [
                'delete' => [
                    'label' => 'Futa',
                ],
            ],
        ],

        'notifications' => [
            'deleted' => [
                'title' => 'Imefutwa',
            ],

            'deleted_partial' => [
                'title' => 'Imefuta :count kati ya :total',
                'missing_authorization_failure_message' => 'Hauna ruhusa ya kufuta :count.',
                'missing_processing_failure_message' => ':count haikuweza kufutwa.',
            ],

            'deleted_none' => [
                'title' => 'Imeshindwa kufuta',
                'missing_authorization_failure_message' => 'Hauna ruhusa ya kufuta :count.',
                'missing_processing_failure_message' => ':count haikuweza kufutwa.',
            ],
        ],
    ],

];
