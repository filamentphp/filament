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

            'requires_confirmation_subheading' => 'Вы уверены, что хотите это сделать?',

            'buttons' => [

                'cancel' => [
                    'label' => 'Отменить',
                ],

                'confirm' => [
                    'label' => 'Подтвердить',
                ],

                'submit' => [
                    'label' => 'Отправить',
                ],

            ],

        ],

    ],

    'empty' => [
        'heading' => 'не найдено записей',
    ],

    'filters' => [

        'buttons' => [

            'reset' => [
                'label' => 'Сбросить фильтры',
            ],

        ],

    ],

    'selection_indicator' => [

        'selected_count' => 'Выбрана 1 запись.|Выбрано :count записей.',

        'buttons' => [

            'select_all' => [
                'label' => 'Выбрать все :count',
            ],

            'deselect_all' => [
                'label' => 'Убрать выделение со всех',
            ],

        ],

    ],

];
