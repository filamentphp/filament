<?php

return [

    'fields' => [

        'search_query' => [
            'label' => 'Поиск',
            'placeholder' => 'Поиск',
        ],

    ],

    'pagination' => [

        'label' => 'навигация разбивки на страницы',

        'overview' => 'показ :first до :last из :total результаты',

        'fields' => [

            'records_per_page' => [
                'label' => 'на страницу',
            ],

        ],

        'buttons' => [

            'go_to_page' => [
                'label' => 'Перейти к странице :page',
            ],

            'next' => [
                'label' => 'Следующая',
            ],

            'previous' => [
                'label' => 'Предыдущая',
            ],

        ],

    ],

    'buttons' => [

        'filter' => [
            'label' => 'Фильтр',
        ],

        'open_actions' => [
            'label' => 'Open actions',
        ],

    ],

    'actions' => [

        'modal' => [

            'requires_confirmation_subheading' => 'Are you sure you would like to do this?',

            'buttons' => [

                'cancel' => [
                    'label' => 'Отменить',
                ],

                'confirm' => [
                    'label' => 'Confirm',
                ],

                'submit' => [
                    'label' => 'Submit',
                ],

            ],

        ],

    ],

    'empty' => [
        'heading' => 'не найдено записей',
    ],

    'selection_indicator' => [

        'buttons' => [

            'select_all' => [
                'label' => 'Select all :count',
            ],

        ],

    ],

];
