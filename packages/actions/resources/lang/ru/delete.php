<?php

return [

    'single' => [

        'label' => 'Удалить',

        'modal' => [

            'heading' => 'Удалить :label',

            'actions' => [

                'delete' => [
                    'label' => 'Удалить',
                ],

            ],

        ],

        'notifications' => [

            'deleted' => [
                'title' => 'Удалено',
            ],

        ],

    ],

    'multiple' => [

        'label' => 'Удалить отмеченное',

        'modal' => [

            'heading' => 'Удалить отмеченное :label',

            'actions' => [

                'delete' => [
                    'label' => 'Удалить отмеченное',
                ],

            ],

        ],

        'notifications' => [

            'deleted' => [
                'title' => 'Удалено',
            ],

            'deleted_partial' => [
                'title' => 'Удалено :count из :total',
                'missing_authorization_failure_message' => 'У вас нет прав для удаления :count.',
                'missing_processing_failure_message' => ':count не может быть удалено.',
            ],

            'deleted_none' => [
                'title' => 'Не удалось удалить',
                'missing_authorization_failure_message' => 'У вас нет прав для удаления :count.',
                'missing_processing_failure_message' => ':count не может быть удалено.',
            ],

        ],

    ],

];
