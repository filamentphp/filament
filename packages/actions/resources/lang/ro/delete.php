<?php

return [

    'single' => [

        'label' => 'Ștergere',

        'modal' => [

            'heading' => 'Ștergere :label',

            'actions' => [

                'delete' => [
                    'label' => 'Ștergere',
                ],

            ],

        ],

        'notifications' => [

            'deleted' => [
                'title' => 'Șters cu succes',
            ],

        ],

    ],

    'multiple' => [

        'label' => 'Ștergeți înregistrările selectate',

        'modal' => [

            'heading' => 'Ștergeți :label selectate',

            'actions' => [

                'delete' => [
                    'label' => 'Ștergere',
                ],

            ],

        ],

        'notifications' => [

            'deleted' => [
                'title' => 'Șters cu succes',
            ],

            'deleted_partial' => [
                'title' => 'S-au șters :count din :total',
                'missing_authorization_failure_message' => 'Nu aveți permisiunea de a șterge :count.',
                'missing_processing_failure_message' => ':count nu au putut fi șterse.',
            ],

            'deleted_none' => [
                'title' => 'Ștergerea a eșuat',
                'missing_authorization_failure_message' => 'Nu aveți permisiunea de a șterge :count.',
                'missing_processing_failure_message' => ':count nu au putut fi șterse.',
            ],

        ],

    ],

];
