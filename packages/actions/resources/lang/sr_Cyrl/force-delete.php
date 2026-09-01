<?php

return [

    'single' => [

        'label' => 'Присилно избриши',

        'modal' => [

            'heading' => 'Присилно избриши :label',

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

        'label' => 'Присилно избриши изабрано',

        'modal' => [

            'heading' => 'Присилно избриши изабрано',

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
