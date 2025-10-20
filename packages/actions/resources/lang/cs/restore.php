<?php

return [

    'single' => [

        'label' => 'Obnovit',

        'modal' => [

            'heading' => 'Obnovit :label',

            'actions' => [

                'restore' => [
                    'label' => 'Obnovit',
                ],

            ],

        ],

        'notifications' => [

            'restored' => [
                'title' => 'Obnoveno',
            ],

        ],

    ],

    'multiple' => [

        'label' => 'Obnovit vybrané',

        'modal' => [

            'heading' => 'Obnovit vybrané :label',

            'actions' => [

                'restore' => [
                    'label' => 'Obnovit',
                ],

            ],

        ],

        'notifications' => [

            'restored' => [
                'title' => 'Obnoveno',
            ],

            'restored_partial' => [
                'title' => 'Obnoveno :count z :total',
                'missing_authorization_failure_message' => 'Nemáte oprávnění obnovit :count.',
                'missing_processing_failure_message' => ':count se nepodařilo obnovit.',
            ],

            'restored_none' => [
                'title' => 'Obnovení selhalo',
                'missing_authorization_failure_message' => 'Nemáte oprávnění obnovit :count.',
                'missing_processing_failure_message' => ':count se nepodařilo obnovit.',
            ],

        ],

    ],

];
