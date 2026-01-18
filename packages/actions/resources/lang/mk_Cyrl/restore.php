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
                'title' => 'Вратено',
            ],

        ],

    ],

    'multiple' => [

        'label' => 'Врати избрани',

        'modal' => [

            'heading' => 'Врати избрани :label',

            'actions' => [

                'restore' => [
                    'label' => 'Врати',
                ],

            ],

        ],

        'notifications' => [

            'restored' => [
                'title' => 'Вратено',
            ],

            'restored_partial' => [
                'title' => 'Вратени :count од :total',
                'missing_authorization_failure_message' => 'Немате дозвола да вратите :count.',
                'missing_processing_failure_message' => ':count не можаа да бидат вратени.',
            ],

            'restored_none' => [
                'title' => 'Неуспешно враќање',
                'missing_authorization_failure_message' => 'Немате дозвола да вратите :count.',
                'missing_processing_failure_message' => ':count не можаа да бидат вратени.',
            ],

        ],

    ],

];
