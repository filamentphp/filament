<?php

return [

    'single' => [

        'label' => 'Восстановить',

        'modal' => [

            'heading' => 'Восстановить :label',

            'actions' => [

                'restore' => [
                    'label' => 'Восстановить',
                ],

            ],

        ],

        'notifications' => [

            'restored' => [
                'title' => 'Запись восстановлена',
            ],

        ],

    ],

    'multiple' => [

        'label' => 'Восстановить выбранное',

        'modal' => [

            'heading' => 'Восстановить выбранное :label',

            'actions' => [

                'restore' => [
                    'label' => 'Восстановить',
                ],

            ],

        ],

        'notifications' => [

            'restored' => [
                'title' => 'Записи восстановлены',
            ],

            'restored_partial' => [
                'title' => 'Восстановлено :count из :total',
                'missing_authorization_failure_message' => 'У вас нет прав на восстановление :count.',
                'missing_processing_failure_message' => 'Не удалось восстановить :count.',
            ],

            'restored_none' => [
                'title' => 'Failed to restore',
                'missing_authorization_failure_message' => 'У вас нет прав на восстановление :count.',
                'missing_processing_failure_message' => 'Не удалось восстановить :count.',
            ],

        ],

    ],

];
