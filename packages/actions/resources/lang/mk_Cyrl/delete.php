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
                'title' => 'Избришано',
            ],

        ],

    ],

    'multiple' => [

        'label' => 'Избриши избрани',

        'modal' => [

            'heading' => 'Избриши избрани :label',

            'actions' => [

                'delete' => [
                    'label' => 'Избриши',
                ],

            ],

        ],

        'notifications' => [

            'deleted' => [
                'title' => 'Избришано',
            ],

            'deleted_partial' => [
                'title' => 'Избришани :count од :total',
                'missing_authorization_failure_message' => 'Немате дозвола да избришете :count.',
                'missing_processing_failure_message' => ':count не можаа да бидат избришани.',
            ],

            'deleted_none' => [
                'title' => 'Неуспешно бришење',
                'missing_authorization_failure_message' => 'Немате дозвола да избришете :count.',
                'missing_processing_failure_message' => ':count не можаа да бидат избришани.',
            ],

        ],

    ],

];
