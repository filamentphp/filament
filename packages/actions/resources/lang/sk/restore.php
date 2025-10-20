<?php

return [

    'single' => [

        'label' => 'Obnoviť',

        'modal' => [

            'heading' => 'Obnoviť :label',

            'actions' => [

                'restore' => [
                    'label' => 'Obnoviť',
                ],

            ],

        ],

        'notifications' => [

            'restored' => [
                'title' => 'Obnovené',
            ],

        ],

    ],

    'multiple' => [

        'label' => 'Obnoviť vybrané',

        'modal' => [

            'heading' => 'Obnoviť vybrané :label',

            'actions' => [

                'restore' => [
                    'label' => 'Obnoviť',
                ],

            ],

        ],

        'notifications' => [

            'restored' => [
                'title' => 'Obnovené',
            ],

            'restored_partial' => [
                'title' => 'Obnovených :count z :total',
                'missing_authorization_failure_message' => 'Nemáte oprávnenie obnoviť :count.',
                'missing_processing_failure_message' => ':count sa nepodarilo obnoviť.',
            ],

            'restored_none' => [
                'title' => 'Obnovenie zlyhalo',
                'missing_authorization_failure_message' => 'Nemáte oprávnenie obnoviť :count.',
                'missing_processing_failure_message' => ':count sa nepodarilo obnoviť.',
            ],

        ],

    ],

];
