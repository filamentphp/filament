<?php

return [

    'single' => [

        'label' => 'Ripristina',

        'modal' => [

            'heading' => 'Ripristina :label',

            'actions' => [

                'restore' => [
                    'label' => 'Ripristina',
                ],

            ],

        ],

        'notifications' => [

            'restored' => [
                'title' => 'Ripristinato',
            ],

        ],

    ],

    'multiple' => [

        'label' => 'Ripristina selezionati',

        'modal' => [

            'heading' => 'Ripristina selezionati :label',

            'actions' => [

                'restore' => [
                    'label' => 'Ripristina',
                ],

            ],

        ],

        'notifications' => [

            'restored' => [
                'title' => 'Ripristinati',
            ],

            'restored_partial' => [
                'title' => 'Ripristinati :count di :total',
                'missing_authorization_failure_message' => 'Non hai il permesso di ripristinare :count.',
                'missing_processing_failure_message' => ':count non possono essere ripristinati.',
            ],

            'restored_none' => [
                'title' => 'Ripristino fallito',
                'missing_authorization_failure_message' => 'Non hai il permesso di ripristinare :count.',
                'missing_processing_failure_message' => ':count non possono essere ripristinati.',
            ],

        ],

    ],

];
