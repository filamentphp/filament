<?php

return [

    'column_manager' => [

        'heading' => 'Kolumny',

        'position' => 'Pozycja :position z :count.',

        'position_in_group' => 'Pozycja :position z :count w :group.',

        'reordered' => ':column przeniesiono na pozycję :position z :count.',

        'reordered_deferred' => ':column przeniesiono na pozycję :position z :count. Zastosuj kolumny, aby zapisać zmiany.',

        'reordered_in_group' => ':column przeniesiono na pozycję :position z :count w :group.',

        'reordered_in_group_deferred' => ':column przeniesiono na pozycję :position z :count w :group. Zastosuj kolumny, aby zapisać zmiany.',

        'actions' => [

            'apply' => [
                'label' => 'Zastosuj kolumny',
            ],

            'move_up' => [
                'label' => 'Przenieś :column w górę',
            ],

            'move_down' => [
                'label' => 'Przenieś :column w dół',
            ],

            'reorder' => [
                'label' => 'Zmień kolejność kolumn',
            ],

            'reset' => [
                'label' => 'Zresetuj',
            ],

        ],

    ],

    'columns' => [

        'actions' => [
            'label' => 'Akcja|Akcje',
        ],

        'editable' => [
            'saving' => 'Zapisywanie zmiany...',
            'saved' => 'Zmiana zapisana.',
            'failure' => 'Nie udało się potwierdzić zapisu. Odśwież stronę i spróbuj ponownie.',
            'unavailable' => 'Nie można już edytować tego elementu.',
        ],

        'icon' => [

            'boolean' => [
                'true' => 'Tak',
                'false' => 'Nie',
            ],

        ],

        'select' => [

            'loading_message' => 'Ładowanie...',

            'no_options_message' => 'Brak dostępnych opcji.',

            'no_search_results_message' => 'Brak wyników.',

            'placeholder' => 'Wybierz opcję',

            'searching_message' => 'Ładowanie...',

            'search_prompt' => 'Zacznij pisać, aby wyszukać...',

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

        'reorder_record' => [
            'label' => 'Zmień kolejność pozycji :key',
        ],

        'move_record_up' => [
            'label' => 'Przenieś :label w górę',
        ],

        'move_record_down' => [
            'label' => 'Przenieś :label w dół',
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

        'column_manager' => [
            'label' => 'Wybierz kolumny',
        ],

        'toggle_record_content' => [
            'label' => 'Rozwiń/zwiń pozycję :key',
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

            'relationship' => [
                'empty_option_label' => 'Brak',
            ],

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

    'loading' => 'Ładowanie...',

    'reorder_indicator' => 'Zmień kolejność przeciągając.',

    'reordering' => [
        'position' => ':label, pozycja :position z :count.',
        'moved' => ':label przeniesiono na pozycję :position z :count.',
    ],

    'result_count' => '{0} Brak wyników|{1} :count wynik|[2,*] :count wyników',

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

    'default_model_label' => 'rekord',

];
