<?php

return [

    'single' => [

        'label' => 'Видалити назавжди',

        'modal' => [

            'heading' => 'Видалити назавжди :label',

            'actions' => [

                'delete' => [
                    'label' => 'Видалити',
                ],

            ],

        ],

        'notifications' => [

            'deleted' => [
                'title' => 'Запис видалено',
            ],

        ],

    ],

    'multiple' => [

        'label' => 'Видалити назавжди обране',

        'modal' => [

            'heading' => 'Видалити назавжди обране :label',

            'actions' => [

                'delete' => [
                    'label' => 'Видалити',
                ],

            ],

        ],

        'notifications' => [

            'deleted' => [
                'title' => 'Записи видалено',
            ],

            'deleted_partial' => [
                'title' => 'Видалено :count із :total',
                'missing_authorization_failure_message' => 'У вас немає прав для видалення :count записів.',
                'missing_processing_failure_message' => 'Не вдалося видалити :count записів.',
            ],

            'deleted_none' => [
                'title' => 'Не вдалося видалити',
                'missing_authorization_failure_message' => 'У вас немає прав для видалення :count записів.',
                'missing_processing_failure_message' => 'Не вдалося видалити :count записів.',
            ],

        ],

    ],

];
