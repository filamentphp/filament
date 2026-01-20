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
                'title' => 'Избришано',
            ],

        ],

    ],

    'multiple' => [

        'label' => 'Присилно избриши избрани',

        'modal' => [

            'heading' => 'Присилно избриши избрани :label',

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
