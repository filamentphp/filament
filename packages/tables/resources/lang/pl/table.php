<?php

return [

    'column_toggle' => [

        'heading' => 'Kolumny',

    ],

    'columns' => [

        'actions' => [
            'label' => 'Akcja|Akcje',
        ],

        'text' => [

            'actions' => [
                'collapse_list' => 'Pokaż :count mniej',
                'expand_list' => 'Pokaż :count więcej',
            ],

            'more_list_items' => 'i :count więcej',

        ],

    ],

    'fields' => [

        'bulk_select_page' => [
            'label' => 'Zaznacz/odznacz wszystkie pozycje dla operacji zbiorczych.',
        ],

        'bulk_select_record' => [
            'label' => 'Zaznacz/odznacz pozycję :key dla operacji zbiorczych.',
        ],

        'bulk_select_group' => [
            'label' => 'Zaznacz/odznacz grupę :title dla operacji zbiorczych.',
        ],

        'search' => [
            'label' => 'Szukaj',
            'placeholder' => 'Szukaj',
            'indicator' => 'Szukaj',
        ],

    ],

    'summary' => [

        'heading' => 'Podsumowanie',

        'subheadings' => [
            'all' => 'Wszystkie :label',
            'group' => 'Grupa :group',
            'page' => 'Bieżąca strona',
        ],

        'summarizers' => [

            'average' => [
                'label' => 'Średnia',
            ],

            'count' => [
                'label' => 'Ilość',
            ],

            'sum' => [
                'label' => 'Suma',
            ],

        ],

    ],

    'actions' => [

        'disable_reordering' => [
            'label' => 'Zakończ zmienianie kolejności',
        ],

        'enable_reordering' => [
            'label' => 'Zmień kolejność',
        ],

        'filter' => [
            'label' => 'Filtr',
        ],

        'group' => [
            'label' => 'Grupa',
        ],

        'open_bulk_actions' => [
            'label' => 'Akcje masowe',
        ],

        'toggle_columns' => [
            'label' => 'Wybierz kolumny',
        ],

    ],

    'empty' => [

        'heading' => 'Nie znaleziono rekordów',

        'description' => 'Utwórz rekord aby rozpocząć.',

    ],

    'filters' => [

        'actions' => [

            'apply' => [
                'label' => 'Zastosuj filtry',
            ],

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

        'heading' => 'Filtry',

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

    'grouping' => [

        'fields' => [

            'group' => [
                'label' => 'Grupuj według',
                'placeholder' => 'Grupuj według',
            ],

            'direction' => [

                'label' => 'Kolejność grup',

                'options' => [
                    'asc' => 'Rosnąco',
                    'desc' => 'Malejąco',
                ],

            ],

        ],

    ],

    'reorder_indicator' => 'Zmień kolejność przeciągając.',

    'selection_indicator' => [

        'selected_count' => '{1} 1 rekord zaznaczony|[2,4]:count rekordy zaznaczone|[5,*]:count rekordów zaznaczonych',

        'actions' => [

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
