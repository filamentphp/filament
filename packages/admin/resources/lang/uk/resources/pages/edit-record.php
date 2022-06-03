<?php

return [

    'title' => 'Змінити :label',

    'breadcrumb' => 'Змінити',

    'actions' => [

        'delete' => [

            'label' => 'Видалити',

            'modal' => [

                'heading' => 'Видалити :label',

                'subheading' => 'Ви впевнені, що хочете це зробити?',

                'buttons' => [

                    'delete' => [
                        'label' => 'Видалити',
                    ],

                ],

            ],

            'messages' => [
                'deleted' => 'Видалено',
            ],

        ],

        'view' => [
            'label' => 'Переглянути',
        ],

    ],

    'form' => [

        'actions' => [

            'cancel' => [
                'label' => 'Скасувати',
            ],

            'save' => [
                'label' => 'Зберегти',
            ],

        ],

    ],

    'messages' => [
        'saved' => 'Збережено',
    ],

];
