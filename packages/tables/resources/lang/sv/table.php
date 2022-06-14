<?php

return [

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

    'actions' => [

        'replicate' => [

            'label' => 'Replikera',

            'messages' => [
                'replicated' => 'Raden replikerades',
            ],

        ],

    ],

    'empty' => [
        'heading' => 'Inga rader hittades',
    ],

    'filters' => [

        'buttons' => [

            'reset' => [
                'label' => 'Återställ filter',
            ],

            'close' => [
                'label' => 'Stäng',
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

];
