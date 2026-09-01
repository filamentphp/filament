<?php

return [

    'single' => [

        'label' => 'Врати',

        'modal' => [

            'heading' => 'Врати :label',

            'actions' => [

                'restore' => [
                    'label' => 'Врати',
                ],

            ],

        ],

        'notifications' => [

            'restored' => [
                'title' => 'Враћено',
            ],

        ],

    ],

    'multiple' => [

        'label' => 'Поврати изабрано',

        'modal' => [

            'heading' => 'Поврати изабрано :label',

            'actions' => [

                'restore' => [
                    'label' => 'Поврати',
                ],

            ],

        ],

        'notifications' => [

            'restored' => [
                'title' => 'Повраћено',
            ],

            'restored_partial' => [
                'title' => 'Повраћено :count од :total',
                'missing_authorization_failure_message' => 'Немате дозволу да повратите :count.',
                'missing_processing_failure_message' => ':count не може да се поврати.',
            ],

            'restored_none' => [
                'title' => 'Враћање није успело',
                'missing_authorization_failure_message' => 'Немате дозволу да повратите :count.',
                'missing_processing_failure_message' => ':count не може да се поврати.',
            ],
        ],

    ],

];
