<?php

return [

    'title' => 'Редактирование :label',

    'breadcrumb' => 'Редактирование',

    'actions' => [

        'delete' => [

            'label' => 'Удалить',

            'modal' => [

                'heading' => 'Удалить :label',

                'subheading' => 'Вы уверены, что хотите это сделать?',

                'buttons' => [

                    'delete' => [
                        'label' => 'Удалить',
                    ],

                ],

            ],

        ],

        'view' => [
            'label' => 'Посмотреть',
        ],

    ],

    'form' => [

        'actions' => [

            'cancel' => [
                'label' => 'Отмена',
            ],

            'save' => [
                'label' => 'Сохранить',
            ],

        ],

    ],

    'messages' => [
        'saved' => 'Сохранено',
    ],

];
