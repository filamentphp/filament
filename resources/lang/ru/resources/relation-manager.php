<?php

return [

    'buttons' => [

        'attach' => [
            'label' => 'присоединить к существующему',
        ],

        'create' => [
            'label' => 'Новое',
        ],

        'detach' => [
            'label' => 'Отсоединить выбранное',
        ],

    ],

    'modals' => [

        'attach' => [

            'buttons' => [

                'cancel' => [
                    'label' => 'Отменить',
                ],

                'attach' => [
                    'label' => 'Присоединить',
                ],

                'attachAnother' => [
                    'label' => 'Присоединить и присоединить другое',
                ],

            ],

            'form' => [

                'related' => [
                    'placeholder' => 'Введите текст для поиска...',
                ],

            ],

            'heading' => 'Присоединить к существующему',

            'messages' => [
                'attached' => 'Присоединено!',
            ],

        ],

        'create' => [

            'buttons' => [

                'cancel' => [
                    'label' => 'Отменить',
                ],

                'create' => [
                    'label' => 'Создать',
                ],

                'createAnother' => [
                    'label' => 'Создать и Создать другое',
                ],

            ],

            'heading' => 'Создать',

            'messages' => [
                'created' => 'Создано!',
            ],

        ],

        'detach' => [

            'buttons' => [

                'cancel' => [
                    'label' => 'Отменить',
                ],

                'detach' => [
                    'label' => 'Отсоединить выбранное',
                ],

            ],

            'description' => 'Вы уверены, что хотите отсоединить выбранные записи? Это действие не может быть отменено.',

            'heading' => 'Отсоединить выбранные записи?',

            'messages' => [
                'detached' => 'Отсоединено!',
            ],

        ],

        'edit' => [

            'buttons' => [

                'cancel' => [
                    'label' => 'Отменить',
                ],

                'save' => [
                    'label' => 'Сохранить',
                ],

            ],

            'heading' => 'Редактировать',

            'messages' => [
                'saved' => 'Сохранено!',
            ],

        ],

    ],

];
