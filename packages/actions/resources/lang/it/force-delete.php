<?php

return [

    'single' => [

        'label' => 'Forza eliminazione',

        'modal' => [

            'heading' => 'Forza eliminazione :label',

            'actions' => [

                'delete' => [
                    'label' => 'Elimina',
                ],

            ],

        ],

        'notifications' => [

            'deleted' => [
                'title' => 'Eliminato',
            ],

        ],

    ],

    'multiple' => [

        'label' => 'Elimina forzatamente selezionati',

        'modal' => [

            'heading' => 'Elimina forzatamente :label selezionati',

            'actions' => [

                'delete' => [
                    'label' => 'Elimina',
                ],

            ],

        ],

        'notifications' => [

            'deleted' => [
                'title' => 'Eliminati',
            ],

            'deleted_partial' => [
                'title' => 'Eliminati :count di :total',
                'missing_authorization_failure_message' => 'Non hai il permesso di eliminare :count.',
                'missing_processing_failure_message' => ':count non possono essere eliminati.',
            ],

            'deleted_none' => [
                'title' => 'Eliminazione fallita',
                'missing_authorization_failure_message' => 'Non hai il permesso di eliminare :count.',
                'missing_processing_failure_message' => ':count non possono essere eliminati.',
            ],

        ],

    ],

];
