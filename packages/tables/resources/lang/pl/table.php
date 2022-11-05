<?php

return [

    'columns' => [

        'tags' => [
            'more' => 'i :count więcej',
        ],

        'messages' => [
            'copied' => 'Skopiowano',
        ],

    ],

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

                'options' => [
                    'all' => 'Wszystkie',
                ],

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

        'disable_reordering' => [
            'label' => 'Zakończ zmienianie kolejności',
        ],

        'enable_reordering' => [
            'label' => 'Zmień kolejność',
        ],

        'filter' => [
            'label' => 'Filtr',
        ],

        'open_actions' => [
            'label' => 'Otwórz akcje',
        ],

        'toggle_columns' => [
            'label' => 'Wybierz kolumny',
        ],

    ],

    'empty' => [
        'heading' => 'Nie znaleziono wyników',
    ],

    'filters' => [

        'buttons' => [

            'remove' => [
                'label' => 'Usuń filtr',
            ],

            'remove_all' => [
                'label' => 'Usuń wszystkie filtry',
                'tooltip' => 'Usuń wszystkie filtry',
            ],

            'reset' => [
                'label' => 'Zresetuj filtry',
            ],

        ],

        'indicator' => 'Aktywne filtry',

        'multi_select' => [
            'placeholder' => 'Wszystkie',
        ],

        'select' => [
            'placeholder' => 'Wszystkie',
        ],

        'trashed' => [

            'label' => 'Usunięte rekordy',

            'only_trashed' => 'Tylko usunięte rekordy',

            'with_trashed' => 'Uwzględnij usunięte rekordy',

            'without_trashed' => 'Bez usuniętych rekordów',

        ],

    ],

    'reorder_indicator' => 'Zmień kolejność przeciągając.',

    'selection_indicator' => [

        'selected_count' => '{1} 1 rekord zaznaczony.|[2,4]:count rekordy zaznaczone.|[5,*]:count rekordów zaznaczonych.',

        'buttons' => [

            'select_all' => [
                'label' => 'Zaznacz wszystkie :count',
            ],

            'deselect_all' => [
                'label' => 'Odznacz wszystkie',
            ],

        ],

    ],

    'sorting' => [

        'fields' => [

            'column' => [
                'label' => 'Sortuj według',
            ],

            'direction' => [

                'label' => 'Kierunek sortowania',

                'options' => [
                    'asc' => 'Rosnąco',
                    'desc' => 'Malejąco',
                ],

            ],

        ],

    ],

];
