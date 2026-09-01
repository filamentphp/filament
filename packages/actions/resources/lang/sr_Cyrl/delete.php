<?php

return [

    'single' => [

        'label' => 'Избриши',

        'modal' => [

            'heading' => 'Избриши :label',

            'actions' => [

                'delete' => [
                    'label' => 'Избриши',
                ],

            ],

        ],

        'notifications' => [

            'deleted' => [
                'title' => 'Избрисано',
            ],

        ],

    ],

    'multiple' => [

        'label' => 'Избриши изабрано',

        'modal' => [

            'heading' => 'Избриши изабрано',

            'actions' => [

                'delete' => [
                    'label' => 'Избриши',
                ],

            ],

        ],

        'notifications' => [

            'deleted' => [
                'title' => 'Избрисано',
            ],

            'deleted_partial' => [
                'title' => 'Избрисано :count од :total',
                'missing_authorization_failure_message' => 'Немате дозволу да избришите :count.',
                'missing_processing_failure_message' => ':count не може да се избрише.',
            ],

            'deleted_none' => [
                'title' => 'Брисање није успело',
                'missing_authorization_failure_message' => 'Немате дозволу да избришите :count.',
                'missing_processing_failure_message' => ':count не може да се избрише.',
            ],

        ],

    ],

];
