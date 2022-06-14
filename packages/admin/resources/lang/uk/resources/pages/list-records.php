<?php

return [

    'breadcrumb' => 'Перегляд',

    'actions' => [

        'create' => [

            'label' => 'Створити :label',

            'modal' => [

                'heading' => 'Створити :label',

                'actions' => [

                    'create' => [
                        'label' => 'Створити',
                    ],

                    'create_and_create_another' => [
                        'label' => 'Створити та створити наступне',
                    ],

                ],

            ],

            'messages' => [
                'created' => 'Створено',
            ],

        ],

    ],

    'table' => [

        'actions' => [

            'delete' => [

                'label' => 'Видалити',

                'modal' => [
                    'heading' => 'Видалити :label',
                ],

                'messages' => [
                    'deleted' => 'Видалено',
                ],

            ],

            'edit' => [

                'label' => 'Змінити',

                'modal' => [

                    'heading' => 'Змінити :label',

                    'actions' => [

                        'save' => [
                            'label' => 'Зберегти',
                        ],

                    ],

                ],

                'messages' => [
                    'saved' => 'Збережено',
                ],

            ],

            'view' => [

                'label' => 'Перегляд',

                'modal' => [

                    'heading' => 'Переглянути :label',

                    'actions' => [

                        'close' => [
                            'label' => 'Закрити',
                        ],

                    ],

                ],

            ],

        ],

        'bulk_actions' => [

            'delete' => [

                'label' => 'Видалити вибране',

                'messages' => [
                    'deleted' => 'Видалено',
                ],

            ],

        ],

    ],

];
