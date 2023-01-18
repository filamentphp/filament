<?php

return [

    'columns' => [

        'tags' => [
            'more' => 'och :count till',
        ],

        'messages' => [
            'copied' => 'Kopierad',
        ],

    ],

    'fields' => [

        'search_query' => [
            'label' => 'Sök',
            'placeholder' => 'Sök',
        ],

    ],

    'pagination' => [

        'label' => 'Meny för sidnumerering',

        'overview' => 'Visar :first till :last av :total resultat',

        'fields' => [

            'records_per_page' => [

                'label' => 'per sida',

                'options' => [
                    'all' => 'Alla',
                ],

            ],

        ],

        'buttons' => [

            'go_to_page' => [
                'label' => 'Gå till sida :page',
            ],

            'next' => [
                'label' => 'Nästa',
            ],

            'previous' => [
                'label' => 'Föregående',
            ],

        ],

    ],

    'buttons' => [

        'disable_reordering' => [
            'label' => 'Sluta ändra ordning på rader',
        ],

        'enable_reordering' => [
            'label' => 'Ändra ordning på rader',
        ],

        'filter' => [
            'label' => 'Filter',
        ],

        'open_actions' => [
            'label' => 'Öppna åtgärder',
        ],

        'toggle_columns' => [
            'label' => 'Växla kolumner',
        ],

    ],

    'empty' => [
        'heading' => 'Inga rader hittades',
    ],

    'filters' => [

        'buttons' => [

            'remove' => [
                'label' => 'Ta bort filter',
            ],

            'remove_all' => [
                'label' => 'Ta bort alla filter',
                'tooltip' => 'Ta bort alla filter',
            ],

            'reset' => [
                'label' => 'Återställ filter',
            ],

        ],

        'indicator' => 'Aktiva filter',

        'multi_select' => [
            'placeholder' => 'Alla',
        ],

        'select' => [
            'placeholder' => 'Alla',
        ],

        'trashed' => [

            'label' => 'Raderade rader',

            'only_trashed' => 'Endast raderade rader',

            'with_trashed' => 'Med raderade rader',

            'without_trashed' => 'Utan raderade rader',

        ],

    ],

    'reorder_indicator' => 'Dra och släpp raderna i önskad ordning.',

    'selection_indicator' => [

        'selected_count' => '1 rad vald.|:count rader valda.',

        'buttons' => [

            'select_all' => [
                'label' => 'Välj alla :count',
            ],

            'deselect_all' => [
                'label' => 'Avmarkera alla',
            ],

        ],

    ],

    'sorting' => [

        'fields' => [

            'column' => [
                'label' => 'Sortera efter',
            ],

            'direction' => [

                'label' => 'Sorteringsriktning',

                'options' => [
                    'asc' => 'Stigande',
                    'desc' => 'Fallande',
                ],

            ],

        ],

    ],

];
