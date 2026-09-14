<?php

return [

    'single' => [
        'label' => 'Futa kwa lazima',

        'modal' => [
            'heading' => 'Futa kwa lazima :label',

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
        'label' => 'Futa kwa lazima chaguo',

        'modal' => [
            'heading' => 'Futa kwa lazima chaguo :label',

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
                'title' => 'Imefuta kabisa :count kati ya :total',
                'missing_authorization_failure_message' => 'Hauna ruhusa ya kufuta kabisa :count.',
                'missing_processing_failure_message' => ':count haikuweza kufutwa kabisa.',
            ],

            'deleted_none' => [
                'title' => 'Imeshindwa kufuta kabisa',
                'missing_authorization_failure_message' => 'Hauna ruhusa ya kufuta kabisa :count.',
                'missing_processing_failure_message' => ':count haikuweza kufutwa kabisa.',
            ],
        ],
    ],

];
