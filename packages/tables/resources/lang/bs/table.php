<?php

return [

    'fields' => [

        'search_query' => [
            'label' => 'Pretraga',
            'placeholder' => 'Traži',
        ],

    ],

    'pagination' => [

        'label' => 'Navigacija po stranicama',

        'overview' => 'Prikazivanje :first od :last od ukupno :total rezultata',

        'fields' => [

            'records_per_page' => [
                'label' => 'po stranici',
            ],

        ],

        'buttons' => [

            'go_to_page' => [
                'label' => 'Idi na stranu :page',
            ],

            'next' => [
                'label' => 'Dalje',
            ],

            'previous' => [
                'label' => 'Nazad',
            ],

        ],

    ],

    'buttons' => [

        'filter' => [
            'label' => 'Filter',
        ],

        'open_actions' => [
            'label' => 'Otvorene akcije',
        ],

        'toggle_columns' => [
            'label' => 'Uključi/isključi kolone',
        ],

    ],

    'empty' => [
        'heading' => 'Nije pronađen nijedan zapis',
    ],

    'filters' => [

        'buttons' => [

            'reset' => [
                'label' => 'Resetujte filtere',
            ],

        ],

        'multi_select' => [
            'placeholder' => 'Svi',
        ],

        'select' => [
            'placeholder' => 'Sve/svi',
        ],

        'trashed' => [

            'label' => 'Izbrisani zapisi',

            'only_trashed' => 'Samo izbrisani zapisi',

            'with_trashed' => 'Sa izbrisanim zapisima',

            'without_trashed' => 'Bez izbrisanih zapisa',

        ],

    ],

    'selection_indicator' => [

        'selected_count' => '1 odabran zapis.|:count odabrani zapisi.',

        'buttons' => [

            'select_all' => [
                'label' => 'Odaberi sve :count',
            ],

            'deselect_all' => [
                'label' => 'Opozovite izbor',
            ],

        ],

    ],

];
