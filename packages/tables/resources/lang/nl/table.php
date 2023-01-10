<?php

return [

    'columns' => [

        'tags' => [
            'more' => 'en :count meer',
        ],

        'messages' => [
            'copied' => 'Gekopieerd',
        ],

    ],

    'fields' => [

        'search_query' => [
            'label' => 'Zoeken',
            'placeholder' => 'Zoeken',
        ],

    ],

    'pagination' => [

        'label' => 'Paginering navigatie',

        'overview' => 'Toont :first tot :last van :total resultaten',

        'fields' => [

            'records_per_page' => [

                'label' => 'per pagina',

                'options' => [
                    'all' => 'Alles',
                ],

            ],

        ],

        'buttons' => [

            'go_to_page' => [
                'label' => 'Ga naar pagina :page',
            ],

            'next' => [
                'label' => 'Volgende',
            ],

            'previous' => [
                'label' => 'Vorige',
            ],

        ],

    ],

    'buttons' => [

        'disable_reordering' => [
            'label' => 'Herordenen van records voltooien',
        ],

        'enable_reordering' => [
            'label' => 'Records herordenen',
        ],

        'filter' => [
            'label' => 'Filteren',
        ],

        'open_actions' => [
            'label' => 'Acties openen',
        ],

        'toggle_columns' => [
            'label' => 'Kolommen in-/uitschakelen',
        ],

    ],

    'empty' => [
        'heading' => 'Geen records gevonden',
    ],

    'filters' => [

        'buttons' => [

            'remove' => [
                'label' => 'Filter verwijderen',
            ],

            'remove_all' => [
                'label' => 'Alle filters verwijderen',
                'tooltip' => 'Alle filters verwijderen',
            ],

            'reset' => [
                'label' => 'Filters resetten',
            ],

        ],

        'indicator' => 'Actieve filters',

        'multi_select' => [
            'placeholder' => 'Alles',
        ],

        'select' => [
            'placeholder' => 'Alles',
        ],

        'trashed' => [

            'label' => 'Verwijderde records',

            'only_trashed' => 'Alleen verwijderde records',

            'with_trashed' => 'Met verwijderde records',

            'without_trashed' => 'Zonder verwijderde records',

        ],

    ],

    'reorder_indicator' => 'Sleep de records in de juiste volgorde.',

    'selection_indicator' => [

        'selected_count' => '1 record geselecteerd.|:count records geselecteerd.',

        'buttons' => [

            'select_all' => [
                'label' => 'Selecteer alle :count',
            ],

            'deselect_all' => [
                'label' => 'Alles deselecteren',
            ],

        ],

    ],

    'sorting' => [

        'fields' => [

            'column' => [
                'label' => 'Sorteren op',
            ],

            'direction' => [

                'label' => 'Sorteerrichting',

                'options' => [
                    'asc' => 'Oplopend',
                    'desc' => 'Aflopend',
                ],

            ],

        ],

    ],

];
