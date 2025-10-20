<?php

return [

    'single' => [

        'label' => 'Відновити',

        'modal' => [

            'heading' => 'Відновити :label',

            'actions' => [

                'restore' => [
                    'label' => 'Відновити',
                ],

            ],

        ],

        'notifications' => [

            'restored' => [
                'title' => 'Запис відновлено',
            ],

        ],

    ],

    'multiple' => [

        'label' => 'Відновити вибране',

        'modal' => [

            'heading' => 'Відновити вибране :label',

            'actions' => [

                'restore' => [
                    'label' => 'Відновити',
                ],

            ],

        ],

        'notifications' => [

            'restored' => [
                'title' => 'Записи відновлені',
            ],

            'restored_partial' => [
                'title' => 'Відновлено :count із :total',
                'missing_authorization_failure_message' => 'У вас немає прав для відновлення :count записів.',
                'missing_processing_failure_message' => 'Не вдалося відновити :count записів.',
            ],

            'restored_none' => [
                'title' => 'Не вдалося відновити',
                'missing_authorization_failure_message' => 'У вас немає прав для відновлення :count записів.',
                'missing_processing_failure_message' => 'Не вдалося відновити :count записів.',
            ],

        ],

    ],

];
