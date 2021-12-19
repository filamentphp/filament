<?php

return [

    'fields' => [

        'search_query' => [
            'label' => 'Szukaj',
            'placeholder' => 'Szukaj',
        ],

    ],

    'pagination' => [

        'label' => 'Paginacja',

        'overview' => 'Pozycje od :first do :last z :total łącznie',

        'fields' => [

            'records_per_page' => [
                'label' => 'na stronę',
            ],

        ],

        'buttons' => [

            'go_to_page' => [
                'label' => 'Przejdź do strony :page',
            ],

            'next' => [
                'label' => 'Następna',
            ],

            'previous' => [
                'label' => 'Poprzednia',
            ],

        ],

    ],

    'buttons' => [

        'filter' => [
            'label' => 'Filtr',
        ],

        'open_actions' => [
            'label' => 'Otwórz akcję',
        ],

    ],

    'actions' => [

        'modal' => [

            'requires_confirmation_subheading' => 'Czy na pewno chcesz to zrobić?',

            'buttons' => [

                'cancel' => [
                    'label' => 'Anuluj',
                ],

                'confirm' => [
                    'label' => 'Potwierdź',
                ],

                'submit' => [
                    'label' => 'Zatwierdź',
                ],

            ],

        ],

    ],

    'empty' => [
        'heading' => 'Nie znaleziono wyników',
    ],

    'selection_indicator' => [

        'buttons' => [

            'select_all' => [
                'label' => 'Zaznacz wszystkie :count',
            ],

        ],

    ],

];
