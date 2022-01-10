<?php

return [

    'fields' => [

        'search_query' => [
            'label' => 'Sök',
            'placeholder' => 'Sök',
        ],

    ],

    'pagination' => [

        'label' => 'Pagineringsmeny',

        'overview' => 'Visar från :first till :last av :total totalt',

        'fields' => [

            'records_per_page' => [
                'label' => 'per sida',
            ],

        ],

        'buttons' => [

            'go_to_page' => [
                'label' => 'Gå till :page',
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

        'filter' => [
            'label' => 'Filter',
        ],

        'open_actions' => [
            'label' => 'Öppna åtgärder',
        ],

    ],

    'actions' => [

        'modal' => [

            'requires_confirmation_subheading' => 'Är du säker på att du vill göra detta?',

            'buttons' => [

                'cancel' => [
                    'label' => 'Ångra',
                ],

                'confirm' => [
                    'label' => 'Bekräfta',
                ],

                'submit' => [
                    'label' => 'Skicka in',
                ],

            ],

        ],

    ],

    'empty' => [
        'heading' => 'Finns inga rader',
    ],

    'filters' => [

        'buttons' => [

            'reset' => [
                'label' => 'Återställ filter',
            ],

        ],

        'multi_select' => [
            'placeholder' => 'Alla',
        ],

        'select' => [
            'placeholder' => 'Alla',
        ],

    ],

    'selection_indicator' => [

        'buttons' => [

            'select_all' => [
                'label' => 'Välj alla :count',
            ],

        ],

    ],

];
