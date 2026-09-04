<?php

return [

    'single' => [
        'label' => 'Rudisha',

        'modal' => [
            'heading' => 'Rudisha :label',

            'actions' => [
                'restore' => [
                    'label' => 'Rudisha',
                ],
            ],
        ],

        'notifications' => [
            'restored' => [
                'title' => 'Imerudishwa',
            ],
        ],
    ],

    'multiple' => [
        'label' => 'Rudisha chaguo',

        'modal' => [
            'heading' => 'Rudisha chaguo :label',

            'actions' => [
                'restore' => [
                    'label' => 'Rudisha',
                ],
            ],
        ],

        'notifications' => [
            'restored' => [
                'title' => 'Imerudishwa',
            ],

            'restored_partial' => [
                'title' => 'Imerejesha :count kati ya :total',
                'missing_authorization_failure_message' => 'Hauna ruhusa ya kurejesha :count.',
                'missing_processing_failure_message' => ':count haikuweza kurejeshwa.',
            ],

            'restored_none' => [
                'title' => 'Imeshindwa kurejesha',
                'missing_authorization_failure_message' => 'Hauna ruhusa ya kurejesha :count.',
                'missing_processing_failure_message' => ':count haikuweza kurejeshwa.',
            ],
        ],
    ],

];
