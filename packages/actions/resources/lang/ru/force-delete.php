<?php

return [

    'single' => [

        'label' => 'Удалить навсегда',

        'modal' => [

            'heading' => 'Удалить навсегда :label',

            'actions' => [

                'delete' => [
                    'label' => 'Удалить',
                ],

            ],

        ],

        'notifications' => [

            'deleted' => [
                'title' => 'Запись удалена',
            ],

        ],

    ],

    'multiple' => [

        'label' => 'Удалить навсегда выбранное',

        'modal' => [

            'heading' => 'Удалить навсегда выбранное :label',

            'actions' => [

                'delete' => [
                    'label' => 'Удалить',
                ],

            ],

        ],

        'notifications' => [

            'deleted' => [
                'title' => 'Записи удалены',
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
